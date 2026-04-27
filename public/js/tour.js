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

    // 3. Actualizar el cuadro de resumen
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

        const total = selectedSeats.length * pricePerSeat;
        const formattedTotal = `$${total.toLocaleString('es-MX')}`;
        
        subtotalEl.innerText = selectedSeats.length > 0 
            ? `${selectedSeats.length}x $${pricePerSeat.toLocaleString('es-MX')} = ${formattedTotal} MXN`
            : '$0';
        totalEl.innerText = `${formattedTotal} MXN`;
        
        // Update modal total too
        if (totalModalEl) {
            totalModalEl.innerText = `${formattedTotal} MXN`;
        }
        
        // Actualizar input oculto para el formulario
        selectedSeatsInput.value = selectedSeats.join(',');
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
