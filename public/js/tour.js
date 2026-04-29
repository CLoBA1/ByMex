document.addEventListener('DOMContentLoaded', () => {
    const tourId = document.getElementById('tourId').value;
    const pricePerSeat = parseFloat(document.getElementById('tourPrice').value);
    
    const seats = document.querySelectorAll('.seat');
    const selectedSeatsContainer = document.getElementById('selectedSeatsList');
    const subtotalEl = document.getElementById('subtotal');
    const totalEl = document.getElementById('total');
    const totalModalEl = document.getElementById('totalModal');
    const selectedSeatsInput = document.getElementById('selectedSeatsInput');
    const btnContinuar = document.getElementById('btnContinuar');
    
    // Modal elements
    const modal = document.getElementById('checkoutModal');
    const closeModal = document.getElementById('closeModal');
    
    let selectedSeats = [];

    // 1. Cargar estado de los asientos desde la API
    const fetchUrl = window.API_URL_SEATS || `/api/seats/${tourId}`;
    fetch(fetchUrl)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error(data.error);
                return;
            }
            // data es un objeto: { "1": "paid", "5": "pending" }
            seats.forEach(seat => {
                const seatNumber = seat.dataset.seat;
                if (data[seatNumber]) {
                    seat.classList.add(data[seatNumber]); // 'paid' o 'pending'
                }
            });
        })
        .catch(err => console.error("Error fetching seats:", err));

    // 2. Manejar la selección de asientos
    seats.forEach(seat => {
        seat.addEventListener('click', () => {
            // Ignorar si está ocupado o pendiente
            if (seat.classList.contains('occupied') || seat.classList.contains('pending') || seat.classList.contains('paid')) {
                return;
            }

            const seatNumber = seat.dataset.seat;
            
            if (seat.classList.contains('selected')) {
                // Deseleccionar
                seat.classList.remove('selected');
                selectedSeats = selectedSeats.filter(s => s !== seatNumber);
            } else {
                // Seleccionar
                seat.classList.add('selected');
                selectedSeats.push(seatNumber);
            }
            
            updateSummary();
        });
    });

    // 3. Actualizar el cuadro de resumen y Pasajeros
    function updateSummary() {
        // Ordenar los asientos numéricamente
        selectedSeats.sort((a, b) => parseInt(a) - parseInt(b));
        
        selectedSeatsContainer.innerHTML = '';
        
        if (selectedSeats.length === 0) {
            selectedSeatsContainer.innerHTML = '<span class="empty-msg">Selecciona tus asientos arriba</span>';
            btnContinuar.disabled = true;
        } else {
            selectedSeats.forEach(seat => {
                const sn = seat.toString().padStart(2, '0');
                selectedSeatsContainer.innerHTML += `<div class="badge-seat">${sn}</div>`;
            });
            btnContinuar.disabled = false;
        }

        // Actualizar input oculto para el formulario
        selectedSeatsInput.value = selectedSeats.join(',');

        // Generar formulario dinámico de pasajeros
        updatePassengersForm();
        
        // Calcular totales
        calculateTotalWithDiscounts();
    }

    function updatePassengersForm() {
        const passengersContainer = document.getElementById('passengersContainer');
        if (!passengersContainer) return;

        // Guardar estado actual para no borrar lo que el usuario ya escribió si selecciona un asiento extra
        const existingBlocks = {};
        document.querySelectorAll('.passenger-block').forEach(block => {
            const seat = block.dataset.seat;
            const nameInput = block.querySelector('.p-name').value;
            const typeSelect = block.querySelector('.p-type').value;
            existingBlocks[seat] = { name: nameInput, type: typeSelect };
        });

        if (selectedSeats.length === 0) {
            passengersContainer.innerHTML = '';
            return;
        }

        passengersContainer.innerHTML = '<h3 style="margin-top: 1rem; margin-bottom: 0.5rem; font-size: 1.05rem; border-bottom: 1px solid var(--border); padding-bottom: 0.5rem; color: var(--navy);"><i class="fa-solid fa-users"></i> Detalle de Pasajeros</h3>';

        selectedSeats.forEach((seat, index) => {
            const prevData = existingBlocks[seat] || { name: '', type: 'Adulto' };
            const seatLabel = seat.toString().padStart(2, '0');
            
            const html = `
                <div class="passenger-block" data-seat="${seat}" style="background: var(--bg-body); padding: 0.75rem; border-radius: 8px; margin-bottom: 0.75rem; border: 1px solid var(--border);">
                    <p style="font-size: 0.85rem; font-weight: 700; margin-bottom: 8px; color: var(--navy);">Pasajero del Asiento ${seatLabel}</p>
                    <input type="hidden" name="passengers[${index}][seat_number]" value="${seat}">
                    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                        <div style="flex: 2; min-width: 150px;">
                            <input type="text" class="form-control p-name" name="passengers[${index}][name]" value="${prevData.name}" required placeholder="Nombre completo" style="padding: 0.5rem; font-size: 0.85rem; height: auto;">
                        </div>
                        <div style="flex: 1; min-width: 100px;">
                            <select class="form-control p-type" name="passengers[${index}][passenger_type]" style="padding: 0.5rem; font-size: 0.85rem; height: auto;">
                                <option value="Adulto" ${prevData.type === 'Adulto' ? 'selected' : ''}>Adulto (100%)</option>
                                <option value="Niño" ${prevData.type === 'Niño' ? 'selected' : ''}>Niño 3-10 (50%)</option>
                            </select>
                        </div>
                    </div>
                </div>
            `;
            passengersContainer.insertAdjacentHTML('beforeend', html);
        });

        // Re-atar eventos change a los selects para actualizar el precio en vivo
        document.querySelectorAll('.p-type').forEach(select => {
            select.addEventListener('change', calculateTotalWithDiscounts);
        });
    }

    function calculateTotalWithDiscounts() {
        let finalTotal = 0;
        
        if (selectedSeats.length === 0) {
            subtotalEl.innerText = '$0';
            totalEl.innerText = '$0 MXN';
            if (totalModalEl) totalModalEl.innerText = '$0 MXN';
            return;
        }

        const typeSelects = document.querySelectorAll('.p-type');
        if (typeSelects.length > 0) {
            typeSelects.forEach(select => {
                let pPrice = pricePerSeat;
                if (select.value === 'Niño') {
                    pPrice = pricePerSeat * 0.5;
                }
                finalTotal += pPrice;
            });
        } else {
            // Fallback
            finalTotal = selectedSeats.length * pricePerSeat;
        }

        const formattedTotal = `$${finalTotal.toLocaleString('es-MX')}`;
        
        subtotalEl.innerText = `${selectedSeats.length} Asientos`;
        totalEl.innerText = `${formattedTotal} MXN`;
        
        if (totalModalEl) {
            totalModalEl.innerText = `${formattedTotal} MXN`;
        }
    }

    // Inicializar estado del botón
    updateSummary();

    // 4. Manejo del Modal
    btnContinuar.addEventListener('click', () => {
        if(selectedSeats.length > 0) {
            modal.classList.add('active');
        }
    });

    closeModal.addEventListener('click', () => {
        modal.classList.remove('active');
    });

    // Cerrar al hacer clic fuera del modal
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.classList.remove('active');
        }
    });
});
