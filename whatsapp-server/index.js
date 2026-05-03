const express = require('express');
const { 
    default: makeWASocket, 
    useMultiFileAuthState, 
    DisconnectReason, 
    fetchLatestBaileysVersion,
    makeCacheableSignalKeyStore
} = require('@whiskeysockets/baileys');
const qrcode = require('qrcode');
const qrcodeTerminal = require('qrcode-terminal');
const cors = require('cors');
const bodyParser = require('body-parser');
const pino = require('pino');
const fs = require('fs');
const path = require('path');

const app = express();
const port = process.env.PORT || 3000;

app.use(cors());
app.use(bodyParser.json());

let qrCodeData = null;
let clientStatus = 'NOT_READY'; // NOT_READY, WAITING_SCAN, CONNECTED
let sock = null;

const logger = pino({ level: 'info' });

async function connectToWhatsApp() {
    const { state, saveCreds } = await useMultiFileAuthState(path.join(__dirname, 'sessions'));
    const { version, isLatest } = await fetchLatestBaileysVersion();
    
    console.log(`Using WA v${version.join('.')}, isLatest: ${isLatest}`);

    sock = makeWASocket({
        version,
        logger,
        printQRInTerminal: false,
        auth: {
            creds: state.creds,
            keys: makeCacheableSignalKeyStore(state.keys, logger),
        },
        browser: ['Absensi Sekolah', 'Chrome', '1.0.0']
    });

    sock.ev.on('connection.update', async (update) => {
        const { connection, lastDisconnect, qr } = update;
        
        if (qr) {
            console.log('QR RECEIVED', qr);
            qrcodeTerminal.generate(qr, { small: true });
            qrcode.toDataURL(qr, (err, url) => {
                qrCodeData = url;
                clientStatus = 'WAITING_SCAN';
            });
        }

        if (connection === 'close') {
            const shouldReconnect = lastDisconnect?.error?.output?.statusCode !== DisconnectReason.loggedOut;
            console.log('connection closed due to ', lastDisconnect?.error, ', reconnecting ', shouldReconnect);
            clientStatus = 'NOT_READY';
            qrCodeData = null;
            
            if (shouldReconnect) {
                connectToWhatsApp();
            }
        } else if (connection === 'open') {
            console.log('opened connection');
            clientStatus = 'CONNECTED';
            qrCodeData = null;
        }
    });

    sock.ev.on('creds.update', saveCreds);

    return sock;
}

connectToWhatsApp();

// API Endpoints matching Laravel's WhatsappBroadcast Helper

app.get('/api/qrcode', (req, res) => {
    if (qrCodeData) {
        res.json({ data: { qr: qrCodeData } });
    } else {
        res.json({ data: { qr: null }, message: 'QR Code not available or already scanned' });
    }
});

app.get('/api/scan-status', (req, res) => {
    res.json({ status: clientStatus });
});

app.get('/api/device/status-connected', (req, res) => {
    res.json({ data: clientStatus === 'CONNECTED' });
});

app.get('/api/device/connection-state', (req, res) => {
    res.json(clientStatus === 'CONNECTED');
});

app.post('/api/send/text', async (req, res) => {
    const { from, text } = req.body;
    // Note: Laravel helper uses 'from' as the recipient field
    const to = from;

    if (!to || !text) {
        return res.status(400).json({ status: false, message: 'Recipient (from) and text are required' });
    }

    if (clientStatus !== 'CONNECTED' || !sock) {
        return res.status(500).json({ status: false, message: 'WhatsApp client is not connected' });
    }

    try {
        const jid = to.includes('@s.whatsapp.net') ? to : `${to}@s.whatsapp.net`;
        await sock.sendMessage(jid, { text: text });
        res.json({ status: true, message: 'Message sent successfully' });
    } catch (error) {
        console.error('Error sending message:', error);
        res.status(500).json({ status: false, message: 'Failed to send message', error: error.message });
    }
});

app.get('/api/device/restart', async (req, res) => {
    try {
        if (sock) {
            sock.end();
        }
        connectToWhatsApp();
        res.json({ status: true, message: 'Restarting client...' });
    } catch (error) {
        res.status(500).json({ status: false, message: 'Failed to restart client' });
    }
});

app.get('/api/device/delete', async (req, res) => {
    try {
        if (sock) {
            await sock.logout();
        }
        // Remove session directory
        const sessionPath = path.join(__dirname, 'sessions');
        if (fs.existsSync(sessionPath)) {
            fs.rmSync(sessionPath, { recursive: true, force: true });
        }
        connectToWhatsApp();
        res.json({ status: true, message: 'Logged out and session deleted' });
    } catch (error) {
        res.status(500).json({ status: false, message: 'Failed to delete session' });
    }
});

app.get('/api/device/disconnect', async (req, res) => {
    try {
        if (sock) {
            await sock.logout();
        }
        res.json({ status: true, message: 'Disconnected' });
    } catch (error) {
        res.status(500).json({ status: false, message: 'Failed to disconnect' });
    }
});

app.get('/api/device/status', (req, res) => {
    res.json({ status: clientStatus });
});

app.listen(port, () => {
    console.log(`WhatsApp Server running at http://localhost:${port}`);
});

