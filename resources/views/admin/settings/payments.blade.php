<x-app-layout>
    @section('header-title', 'Configuración de Pagos y Bancos')

    @section('extra-css')
    <style>
        .settings-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            overflow: hidden;
            border: 1px solid var(--border);
        }

        .card-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--border);
            background: #fafafa;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-header h3 {
            font-size: 1.1rem;
            color: var(--navy);
            font-weight: 700;
        }

        .card-body {
            padding: 1.5rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }
        
        .form-grid.full-width {
            grid-template-columns: 1fr;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .form-group label {
            font-weight: 600;
            color: var(--text-main);
            font-size: 0.9rem;
        }

        .form-control {
            padding: 0.75rem;
            border: 1px solid var(--border);
            border-radius: 6px;
            font-family: inherit;
            font-size: 0.95rem;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(214, 40, 40, 0.1);
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: #b51c1c;
        }
        
        .btn-secondary {
            background: #f1f5f9;
            color: var(--text-main);
            border: 1px solid var(--border);
        }

        .btn-secondary:hover {
            background: #e2e8f0;
        }

        .btn-danger {
            background: #fee2e2;
            color: #ef4444;
            border: 1px solid #fecaca;
        }
        
        .btn-danger:hover {
            background: #fecaca;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th, .data-table td {
            padding: 1rem 1.5rem;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }

        .data-table th {
            background: #f8fafc;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
        }

        .data-table tr:hover {
            background: #f8fafc;
        }

        .badge {
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .badge-active { background: #dcfce7; color: #166534; }
        .badge-inactive { background: #f1f5f9; color: #64748b; }

        .modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: white;
            border-radius: 8px;
            width: 100%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);
        }

        .modal-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-body {
            padding: 1.5rem;
        }

        .modal-footer {
            padding: 1.5rem;
            border-top: 1px solid var(--border);
            background: #f8fafc;
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
        }
        
        .alert-success {
            background: #dcfce7;
            color: #166534;
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1.5rem;
            border: 1px solid #bbf7d0;
        }
        
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 1.5rem;
        }
        .checkbox-group input {
            width: 1.2rem;
            height: 1.2rem;
        }
    </style>
    @endsection

    @if(session('success'))
        <div class="alert-success">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif
    
    @if($errors->any())
        <div style="background: #fee2e2; color: #b91c1c; padding: 1rem; border-radius: 6px; margin-bottom: 1.5rem;">
            <ul style="margin-left: 1.5rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="settings-grid">
        {{-- BLOQUE 1: DATOS GENERALES --}}
        <div class="card">
            <div class="card-header">
                <h3><i class="fa-solid fa-building"></i> Datos Generales del Negocio para Pagos</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.settings.payments.update') }}" method="POST">
                    @csrf
                    <div class="form-grid" style="margin-bottom: 1.5rem;">
                        <div class="form-group">
                            <label>Nombre Comercial / Razón Social</label>
                            <input type="text" name="business_name" class="form-control" value="{{ old('business_name', $settings->business_name) }}">
                        </div>
                        <div class="form-group">
                            <label>RFC (Opcional)</label>
                            <input type="text" name="rfc" class="form-control" value="{{ old('rfc', $settings->rfc) }}">
                        </div>
                    </div>
                    
                    <div class="form-grid" style="margin-bottom: 1.5rem;">
                        <div class="form-group">
                            <label>Dirección Física (Opcional)</label>
                            <input type="text" name="address" class="form-control" value="{{ old('address', $settings->address) }}">
                        </div>
                        <div class="form-group">
                            <label>Teléfonos de Contacto (Opcional)</label>
                            <input type="text" name="phones" class="form-control" value="{{ old('phones', $settings->phones) }}" placeholder="Ej: 555-123-4567">
                        </div>
                    </div>

                    <div class="form-grid full-width" style="margin-bottom: 1.5rem;">
                        <div class="form-group">
                            <label>Instrucciones Generales de Pago</label>
                            <textarea name="general_instructions" class="form-control" rows="3">{{ old('general_instructions', $settings->general_instructions) }}</textarea>
                            <small style="color: var(--text-muted);">Aparecerá en la sección de pagos antes de listar las cuentas bancarias.</small>
                        </div>
                        <div class="form-group">
                            <label>Nota Final o Advertencia</label>
                            <textarea name="final_note" class="form-control" rows="2">{{ old('final_note', $settings->final_note) }}</textarea>
                            <small style="color: var(--text-muted);">Ej: "Una vez realizado el depósito, enviar comprobante por WhatsApp..."</small>
                        </div>
                    </div>

                    <div style="text-align: right;">
                        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Guardar Datos Generales</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- BLOQUE 2: CUENTAS BANCARIAS --}}
        <div class="card">
            <div class="card-header">
                <h3><i class="fa-solid fa-building-columns"></i> Cuentas Bancarias</h3>
                <button type="button" class="btn btn-primary" onclick="openBankModal()"><i class="fa-solid fa-plus"></i> Nueva Cuenta</button>
            </div>
            
            <div style="overflow-x: auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Orden</th>
                            <th>Banco</th>
                            <th>Titular</th>
                            <th>No. Cuenta / CLABE</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($banks as $bank)
                            <tr>
                                <td style="font-weight: bold; color: var(--text-muted);">{{ $bank->sort_order }}</td>
                                <td>
                                    <strong>{{ $bank->bank_name }}</strong>
                                    @if($bank->label)
                                        <br><small style="color: var(--text-muted);">{{ $bank->label }}</small>
                                    @endif
                                </td>
                                <td>{{ $bank->account_holder }}</td>
                                <td>
                                    @if($bank->account_number) <div><strong>Cta:</strong> {{ $bank->account_number }}</div> @endif
                                    @if($bank->clabe) <div><strong>CLABE:</strong> {{ $bank->clabe }}</div> @endif
                                    @if($bank->card_number) <div><strong>Tarjeta:</strong> {{ $bank->card_number }}</div> @endif
                                </td>
                                <td>
                                    <span class="badge {{ $bank->is_active ? 'badge-active' : 'badge-inactive' }}">
                                        {{ $bank->is_active ? 'Activa' : 'Inactiva' }}
                                    </span>
                                </td>
                                <td>
                                    <div style="display: flex; gap: 0.5rem;">
                                        <button type="button" class="btn btn-secondary" style="padding: 0.5rem;" onclick="editBankModal({{ $bank->toJson() }})">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>
                                        <form action="{{ route('admin.settings.banks.destroy', $bank->id) }}" method="POST" onsubmit="return confirm('¿Eliminar esta cuenta bancaria?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" style="padding: 0.5rem;">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center; color: var(--text-muted);">No hay cuentas bancarias registradas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- MODAL PARA CUENTAS BANCARIAS --}}
    <div id="bankModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Agregar Cuenta Bancaria</h3>
                <button type="button" onclick="closeBankModal()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--text-muted);">&times;</button>
            </div>
            <form id="bankForm" method="POST" action="{{ route('admin.settings.banks.store') }}">
                @csrf
                <input type="hidden" name="_method" id="bankMethod" value="POST">
                
                <div class="modal-body">
                    <div class="form-grid" style="margin-bottom: 1rem;">
                        <div class="form-group">
                            <label>Nombre del Banco <span style="color:red;">*</span></label>
                            <input type="text" name="bank_name" id="bank_name" class="form-control" required placeholder="Ej: BBVA, Santander, Banamex">
                        </div>
                        <div class="form-group">
                            <label>Titular de la Cuenta <span style="color:red;">*</span></label>
                            <input type="text" name="account_holder" id="account_holder" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-grid" style="margin-bottom: 1rem;">
                        <div class="form-group">
                            <label>Número de Cuenta</label>
                            <input type="text" name="account_number" id="account_number" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>CLABE Interbancaria</label>
                            <input type="text" name="clabe" id="clabe" class="form-control">
                        </div>
                    </div>

                    <div class="form-grid" style="margin-bottom: 1rem;">
                        <div class="form-group">
                            <label>Número de Tarjeta (Opcional)</label>
                            <input type="text" name="card_number" id="card_number" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Etiqueta / Descripción (Opcional)</label>
                            <input type="text" name="label" id="label" class="form-control" placeholder="Ej: Solo transferencias">
                        </div>
                    </div>
                    
                    <div class="form-grid" style="margin-bottom: 1rem;">
                        <div class="form-group">
                            <label>Orden de aparición</label>
                            <input type="number" name="sort_order" id="sort_order" class="form-control" value="0">
                            <small style="color: var(--text-muted);">Menor número aparece primero.</small>
                        </div>
                        
                        <div class="checkbox-group">
                            <input type="checkbox" name="is_active" id="is_active" value="1" checked>
                            <label for="is_active" style="cursor: pointer; user-select: none;">Cuenta Activa (Visible)</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeBankModal()">Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Guardar Cuenta</button>
                </div>
            </form>
        </div>
    </div>

    @section('extra-js')
    <script>
        const modal = document.getElementById('bankModal');
        const form = document.getElementById('bankForm');
        const modalTitle = document.getElementById('modalTitle');
        const methodInput = document.getElementById('bankMethod');

        function openBankModal() {
            modalTitle.textContent = 'Agregar Cuenta Bancaria';
            form.action = "{{ route('admin.settings.banks.store') }}";
            methodInput.value = 'POST';
            form.reset();
            document.getElementById('is_active').checked = true;
            document.getElementById('sort_order').value = 0;
            modal.classList.add('active');
        }

        function editBankModal(bank) {
            modalTitle.textContent = 'Editar Cuenta Bancaria';
            form.action = `/admin/settings/banks/${bank.id}`;
            methodInput.value = 'PUT';
            
            document.getElementById('bank_name').value = bank.bank_name || '';
            document.getElementById('account_holder').value = bank.account_holder || '';
            document.getElementById('account_number').value = bank.account_number || '';
            document.getElementById('clabe').value = bank.clabe || '';
            document.getElementById('card_number').value = bank.card_number || '';
            document.getElementById('label').value = bank.label || '';
            document.getElementById('sort_order').value = bank.sort_order || 0;
            document.getElementById('is_active').checked = bank.is_active ? true : false;
            
            modal.classList.add('active');
        }

        function closeBankModal() {
            modal.classList.remove('active');
        }

        // Close modal if clicked outside
        window.onclick = function(event) {
            if (event.target == modal) {
                closeBankModal();
            }
        }
    </script>
    @endsection
</x-app-layout>
