<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $settings = [
            'store_name'         => Setting::get('store_name', 'POS PRO INDONESIA'),
            'store_address'      => Setting::get('store_address', 'Jl. Sudirman Mega Mall Lt. 1 No. 45, Jakarta'),
            'store_phone'        => Setting::get('store_phone', '0812-9988-7766'),
            'store_email'        => Setting::get('store_email', 'support@pospro.id'),
            'store_footer'       => Setting::get('store_footer', 'Terima kasih telah berbelanja! Simpan struk ini sebagai bukti pembayaran.'),
            'receipt_paper_size' => Setting::get('receipt_paper_size', '80mm'),
            'receipt_auto_print' => Setting::get('receipt_auto_print', '1'),
            'qris_image_path'    => Setting::get('qris_image_path', ''),
        ];

        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        // Validate file upload if present
        $request->validate([
            'qris_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // Handle QRIS image upload
        if ($request->hasFile('qris_image') && $request->file('qris_image')->isValid()) {
            $file     = $request->file('qris_image');
            $filename = 'qris_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images/qris'), $filename);
            Setting::set('qris_image_path', 'images/qris/' . $filename);
        }

        // Save all text settings (excluding file input keys)
        $skip = ['_token', 'qris_image'];
        foreach ($request->except($skip) as $key => $value) {
            Setting::set($key, $value);
        }

        return redirect()->route('settings.index')->with('swal_success', 'Pengaturan toko berhasil disimpan!');
    }
}
