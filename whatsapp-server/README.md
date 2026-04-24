# WhatsApp Broadcast Server

A standalone Node.js server to handle WhatsApp messaging using `whatsapp-web.js` and `Express`.

## Installation

1. Navigate to this directory:
   ```bash
   cd whatsapp-server
   ```
2. Install dependencies (if not already done):
   ```bash
   npm install
   ```

## Running the Server

Start the server using:
```bash
node index.js
```
The server will run at `http://localhost:3000` by default.

## Integration with Laravel

1. Open the **Whatsapp Broadcast** menu in your Laravel application.
2. In the **Konfigurasi Whatsapp Broadcast** form, set:
   - **Nomor Whatsapp**: (Your phone number)
   - **Whatsapp URL Konfigurasi**: `http://localhost` (or your server IP)
   - **Whatsapp PORT Konfigurasi**: `3000`
3. Click **Simpan**.
4. If the status is **Menunggu Scan**, a QR code will appear in the dashboard. Scan it with your WhatsApp app.

## API Endpoints

- `GET /api/qrcode`: Fetch the latest QR code for scanning.
- `GET /api/scan-status`: Check current connection status.
- `POST /api/send/text`: Send a text message.
- `GET /api/device/status-connected`: Check if device is ready.
- `GET /api/device/restart`: Restart the WhatsApp client.
- `GET /api/device/delete`: Logout and clear session.
