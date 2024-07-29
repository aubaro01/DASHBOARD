// Clientes icon

document.getElementById('USER_link').addEventListener('click', function(event) {
    event.preventDefault(); 
    fetch('./php/funcs.php?action=getClients')
        .then(response => response.text())
        .then(data => {
            document.getElementById('content-container').innerHTML = data;
        })
        .catch(error => {
            console.error('Error fetching client list:', error);
            document.getElementById('content-container').innerHTML = '<p>Failed to load client list.</p>';
        });
});
