document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('searchInput');
    const searchResults = document.getElementById('searchResults');

    function fetchServices(query = '') {
        fetch(`search.php?ajax=1&q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                searchResults.innerHTML = '';
                if (data.length === 0) {
                    searchResults.innerHTML = '<div class="col-12"><p class="text-muted">No services found.</p></div>';
                    return;
                }
                
                data.forEach(item => {
                    const card = `
                        <div class="col-md-4 mb-4">
                          <div class="card h-100 shadow-sm">
                            <img src="uploads/${item.image}" class="card-img-top" style="height:200px; object-fit:cover;">
                            <div class="card-body">
                              <h5 class="card-title">${item.title}</h5>
                              <p class="card-text text-truncate">${item.description}</p>
                              <p class="fw-bold text-success">RM ${item.price}</p>
                            </div>
                            <div class="card-footer text-muted small">
                              Provided by: ${item.username}
                            </div>
                          </div>
                        </div>
                    `;
                    searchResults.innerHTML += card;
                });
            })
            .catch(error => console.error('Error fetching data:', error));
    }

    // Initial load
    fetchServices();

    // Event listener for live typing
    searchInput.addEventListener('input', (e) => {
        fetchServices(e.target.value);
    });
});