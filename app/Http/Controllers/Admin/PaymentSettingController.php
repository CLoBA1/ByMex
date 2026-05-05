<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentSetting;
use App\Models\BankAccount;
use Illuminate\Http\Request;

class PaymentSettingController extends Controller
{
    public function index()
    {
        // Singleton pattern: always use ID 1 for general settings
        $settings = PaymentSetting::firstOrCreate(
            ['id' => 1],
            [
                'business_name' => 'ByMex',
                'general_instructions' => 'Por favor realiza tu pago o transferencia a cualquiera de las siguientes cuentas bancarias y conserva tu comprobante.'
            ]
        );

        $banks = BankAccount::orderBy('sort_order')->orderBy('id', 'desc')->get();

        return view('admin.settings.payments', compact('settings', 'banks'));
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'business_name' => 'nullable|string|max:255',
            'rfc' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'phones' => 'nullable|string|max:255',
            'whatsapp_number' => 'nullable|string|max:255',
            'general_instructions' => 'nullable|string',
            'final_note' => 'nullable|string',
            'reservation_policies' => 'nullable|string',
            'cancellation_policies' => 'nullable|string',
            'no_show_policies' => 'nullable|string',
            'refund_policies' => 'nullable|string',
        ]);

        $settings = PaymentSetting::firstOrCreate(['id' => 1]);
        $settings->update($validated);

        return back()->with('success', 'Configuración general de pagos actualizada correctamente.');
    }

    public function storeBank(Request $request)
    {
        $validated = $request->validate([
            'bank_name' => 'required|string|max:255',
            'account_holder' => 'required|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'clabe' => 'nullable|string|max:255',
            'card_number' => 'nullable|string|max:255',
            'label' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        BankAccount::create($validated);

        return back()->with('success', 'Cuenta bancaria agregada correctamente.');
    }

    public function updateBank(Request $request, BankAccount $bank)
    {
        $validated = $request->validate([
            'bank_name' => 'required|string|max:255',
            'account_holder' => 'required|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'clabe' => 'nullable|string|max:255',
            'card_number' => 'nullable|string|max:255',
            'label' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $bank->update($validated);

        return back()->with('success', 'Cuenta bancaria actualizada correctamente.');
    }

    public function destroyBank(BankAccount $bank)
    {
        $bank->delete();
        return back()->with('success', 'Cuenta bancaria eliminada correctamente.');
    }
}
