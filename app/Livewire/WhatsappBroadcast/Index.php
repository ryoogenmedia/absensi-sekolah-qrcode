<?php

namespace App\Livewire\WhatsappBroadcast;

use App\Helpers\WhatsappBroadcast;
use App\Models\WhatsappConfig;
use Exception;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Index extends Component
{
    public $nomorWhatsapp;
    public $whatsappUrl;
    public $whatsappPort;
    public $whatsappConfigId;

    public $isActiveDevice;
    public $statusActive;
    public $scanStatus;

    public $whatsappBaseUrl;
    public $qrCodeImage;

    public function getWhatsappGetBaseUrl(){
        $whatsappBroadcast = new WhatsappBroadcast;
        $config = $whatsappBroadcast->config();

        $this->whatsappBaseUrl = $config['url'];
    }

    public function logoutWhatsapp(){
        $whatsappBoradcast = new WhatsappBroadcast;

        try{
            $whatsappBoradcast->deleteDevice();

        }catch(Exception $e){
            logger()->error(
                '[logout whatsapp] ' .
                    auth()->user()->username .
                    ' gagal logout whatsapp',
                [$e->getMessage()]
            );

            session()->flash('alert', [
                'type' => 'danger',
                'message' => 'Gagal!',
                'detail' => "Logout gagal dijalankan.",
            ]);

            return redirect()->back();
        }

        session()->flash('alert', [
            'type' => 'success',
            'message' => 'Berhasil',
            'detail' => "Logout berhasil dijalankan.",
        ]);

        return redirect()->route('whatsapp-broadcast.index');
    }

    public function restartWhatsapp(){
        $whatsappBroadcast = new WhatsappBroadcast;

        try{
            $whatsappBroadcast->restartDevice();
        }catch(Exception $e){
            logger()->error(
                '[restart whatsapp] ' .
                    auth()->user()->username .
                    ' gagal restart whatsapp',
                [$e->getMessage()]
            );

            session()->flash('alert', [
                'type' => 'danger',
                'message' => 'Gagal!',
                'detail' => "Restart gagal dijalankan.",
            ]);
        }

        session()->flash('alert', [
            'type' => 'success',
            'message' => 'Berhasil',
            'detail' => "Restart berhasil dijalankan.",
        ]);

        return redirect()->route('whatsapp-broadcast.index');
    }

    public function testSendText(){
        $whatsappBroadcast = new WhatsappBroadcast;
        $configWhatsapp = base_whatsapp();
        $phoneNumber = format_number_indonesia($configWhatsapp['phone_number']);

        try{
            $whatsappBroadcast->sendText($phoneNumber, "Whatsapp broadcast berhasil terkirim...");
        }catch(Exception $e){
            logger()->error(
                '[get send text] ' .
                    auth()->user()->username .
                    ' gagal mengirim pesan whatsapp',
                [$e->getMessage()]
            );

            session()->flash('alert', [
                'type' => 'danger',
                'message' => 'Gagal',
                'detail' => "gagal mengirim pesan whatsapp.",
            ]);
        }

        session()->flash('alert', [
            'type' => 'success',
            'message' => 'Berhasil',
            'detail' => "berhasil mengirim pesan whatsapp.",
        ]);
    }

    public function getStatusDeviceWhatsapp(){
        $whatsappBoradcast = new WhatsappBroadcast;

        try{
            $this->statusActive     = json_decode($whatsappBoradcast->getDeviceConnection(), true);
            $this->isActiveDevice   = json_decode($whatsappBoradcast->isDeviceConnected(), true);
            $this->scanStatus       = json_decode($whatsappBoradcast->getScanQrCodeStatus(), true);

            // Fetch QR code if waiting for scan
            if (isset($this->scanStatus['status']) && $this->scanStatus['status'] === 'WAITING_SCAN') {
                $qrResponse = json_decode($whatsappBoradcast->getQrCode(), true);
                if (isset($qrResponse['data']['qr'])) {
                    $this->qrCodeImage = $qrResponse['data']['qr'];
                }
            } else {
                $this->qrCodeImage = null;
            }
        }catch(Exception $e){
            logger()->error(
                '[get whatsapp status] ' .
                    auth()->user()->username .
                    ' gagal mengambil status whatsapp',
                [$e->getMessage()]
            );

            session()->flash('alert', [
                'type' => 'danger',
                'message' => 'Gagal',
                'detail' => "Data berhasil status whatsapp tidak di dapatkan.",
            ]);
        }
    }

    public function save(){
        $this->validate([
            'nomorWhatsapp' => ['required','string','min:2','max:255'],
            'whatsappUrl'   => ['required','string','min:2'],
            'whatsappPort'  => ['required','min:2','max:255'],
        ]);

        try{
            DB::beginTransaction();

            $whatsappConfig = WhatsappConfig::find($this->whatsappConfigId);

            if($whatsappConfig){
                $whatsappConfig->update([
                    'phone_number' => $this->nomorWhatsapp,
                    'url' => $this->whatsappUrl,
                    'port' => $this->whatsappPort,
                ]);
            }else{
                WhatsappConfig::create([
                    'phone_number' => $this->nomorWhatsapp,
                    'url' => $this->whatsappUrl,
                    'port' => $this->whatsappPort,
                ]);
            }

            DB::commit();
        }catch(Exception $e){
            DB::rollBack();

            logger()->error(
                '[pengaturan whatsapp broadcast] ' .
                    auth()->user()->username .
                    ' gagal menyunting pengaturan whatsapp broadcast',
                [$e->getMessage()]
            );

            session()->flash('alert', [
                'type' => 'danger',
                'message' => 'Gagal',
                'detail' => "gagal menyunting whatsapp broadcast.",
            ]);

            return redirect()->route('whatsapp-broadcast.index');
        }

        session()->flash('alert', [
            'type' => 'success',
            'message' => 'Berhasil',
            'detail' => "berhasil menyunting whatsapp broadcast.",
        ]);

        return redirect()->route('whatsapp-broadcast.index');
    }

    public function mount(){
        $this->getStatusDeviceWhatsapp();
        $this->getWhatsappGetBaseUrl();

        $whatsappConfig = WhatsappConfig::first();
        $this->nomorWhatsapp = $whatsappConfig->phone_number ?? null;
        $this->whatsappUrl = $whatsappConfig->url ?? null;
        $this->whatsappPort = $whatsappConfig->port ?? null;
        $this->whatsappConfigId = $whatsappConfig->id ?? null;
    }

    public function render()
    {
        return view('livewire.whatsapp-broadcast.index');
    }
}
