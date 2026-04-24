const express = require('express');
const { Client, LocalAuth } = require('whatsapp-web.js');
const qrcode = require('qrcode');
const qrcodeTerminal = require('qrcode-terminal');
const cors = require('cors');
const bodyParser = require('body-parser');

const app = express();
const port = process.env.PORT || 3000;

app.use(cors());
app.use(bodyParser.json());

let qrCodeData = null;
let clientStatus = 'NOT_READY'; // NOT_READY, WAITING_SCAN, CONNECTED

const client = new Client({
    authStrategy: new LocalAuth({
        dataPath: './sessions'
    }),
    webVersionCache: {
        type: 'remote',
        remotePath: 'https://raw.githubusercontent.com/wppconnect-team/wa-version/main/html/2.2412.54.html',
    },
    puppeteer: {
        headless: true, // Use true for the new headless mode in latest Puppeteer
        handleSIGINT: false,
        args: [
            '--no-sandbox',
            '--disable-setuid-sandbox',
            '--disable-dev-shm-usage',
            '--disable-accelerated-2d-canvas',
            '--no-first-run',
            '--no-zygote',
            '--disable-gpu',
            '--log-level=3', // keep logs clean
            '--no-default-browser-check',
            '--disable-site-isolation-trials',
            '--no-experiments',
            '--ignore-gpu-blacklist',
            '--ignore-certificate-errors',
            '--ignore-certificate-errors-spki-list',
            '--disable-extensions',
            '--disable-default-apps',
            '--enable-features=NetworkService'
        ],
    }
});

client.on('qr', (qr) => {
    console.log('QR RECEIVED', qr);
    qrcodeTerminal.generate(qr, { small: true });
    qrcode.toDataURL(qr, (err, url) => {
        qrCodeData = url;
        clientStatus = 'WAITING_SCAN';
    });
});

client.on('ready', () => {
    console.log('CLIENT IS READY');
    clientStatus = 'CONNECTED';
    qrCodeData = null;
});

client.on('authenticated', () => {
    console.log('AUTHENTICATED');
});

client.on('auth_failure', msg => {
    console.error('AUTHENTICATION FAILURE', msg);
    clientStatus = 'NOT_READY';
});

client.on('disconnected', (reason) => {
    console.log('Client was logged out', reason);
    clientStatus = 'NOT_READY';
    client.initialize();
});

client.initialize();

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

    if (clientStatus !== 'CONNECTED') {
        return res.status(500).json({ status: false, message: 'WhatsApp client is not connected' });
    }

    try {
        const chatId = to.includes('@c.us') ? to : `${to}@c.us`;
        await client.sendMessage(chatId, text);
        res.json({ status: true, message: 'Message sent successfully' });
    } catch (error) {
        console.error('Error sending message:', error);
        res.status(500).json({ status: false, message: 'Failed to send message', error: error.message });
    }
});

app.get('/api/device/restart', async (req, res) => {
    try {
        await client.destroy();
        client.initialize();
        res.json({ status: true, message: 'Restarting client...' });
    } catch (error) {
        res.status(500).json({ status: false, message: 'Failed to restart client' });
    }
});

app.get('/api/device/delete', async (req, res) => {
    try {
        await client.logout();
        res.json({ status: true, message: 'Logged out and session deleted' });
    } catch (error) {
        res.status(500).json({ status: false, message: 'Failed to delete session' });
    }
});

app.get('/api/device/disconnect', async (req, res) => {
    try {
        await client.logout();
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
