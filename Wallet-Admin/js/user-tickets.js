document.addEventListener('DOMContentLoaded', function() {
    checkAuthStatus("index.html");
        loadTickets();
});

function loadTickets() {
    const authToken = localStorage.getItem('authToken');
    
    if (!authToken) {
        showMessage('Authentication required. Redirecting to login...', 'error');
        setTimeout(() => {
            window.location.href = 'index.html';
        }, 1500);
        return;
    }
    
    axios.get(
        baseURL+'/admin/v1/get-tickets.php',
        {
            headers: {
                'Authorization': `Bearer ${authToken}`
            }
        }
    )
    .then(function(response) {
        if (response.data.success) {
            displayTickets(response.data.tickets);
        } else {
            showMessage('Failed to load tickets', 'error');
        }
    })
    .catch(function(error) {
        console.error('Error loading tickets:', error);
        
        if (error.response && error.response.status === 401) {
            showMessage('Your session has expired. Please login again.', 'error');
            localStorage.removeItem('authToken');
            setTimeout(() => {
                window.location.href = 'index.html';
            }, 1500);
        } else {
            showMessage('An error occurred while loading tickets.', 'error');
        }
    });
}

function displayTickets(tickets) {
    const tbody = document.getElementById('tickets-tbody');
   
    
    if (!tickets || tickets.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7">No tickets found</td></tr>';
        return;
    }
    
    tbody.innerHTML = '';
    
    tickets.forEach(ticket => {
        const date = new Date(ticket.created_at);
        const formattedDate = date.toLocaleDateString('en-US', { 
            year: 'numeric', 
            month: 'short', 
            day: 'numeric' 
        });
        
        
        const row = document.createElement('tr');
        
        row.innerHTML = `
            <td>${formattedDate}</td>
            <td>TKT-${ticket.ticket_id}</td>
            <td>${ticket.username} (${ticket.email})</td>
            <td>${ticket.subject}</td>
            <td><div class="message-preview">${ticket.message}</div></td>
            <td class="status-${ticket.status}">${ticket.status}</td>
            <td class="ticket-actions">
                              
                    <button class="action-btn resolve-btn" onclick="updateTicketStatus(${ticket.ticket_id}, 'resolved')">Resolve</button>
                  
               
            </td>
        `;
        
        tbody.appendChild(row);
    });
}

function updateTicketStatus(ticketId, newStatus) {
    const authToken = localStorage.getItem('authToken');
    
    if (!authToken) {
        showMessage('Authentication required', 'error');
        return;
    }
    
    const data = {
        ticket_id: ticketId,
        status: newStatus
    };
    
    axios.post(
        baseURL+'/ticket/v1/add-update-ticket.php',
        data,
        {
            headers: {
                'Authorization': `Bearer ${authToken}`
            }
        }
    )
    .then(function(response) {
        if (response.data.success) {
            showMessage(`Ticket status updated to ${newStatus}`, 'success');
            loadTickets();
        } else {
            showMessage(response.data.message || 'Failed to update ticket status', 'error');
        }
    })
    .catch(function(error) {
        console.error('Error updating ticket status:', error);
        showMessage('An error occurred while updating ticket status', 'error');
    });
}


