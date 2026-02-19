// Function to fetch and render completed examinees
async function fetchAndRenderCompletedExaminees() {
    try {
        const response = await fetch('php/dashboard.php'); // Adjust endpoint if needed (e.g., add a query param for 'completed' only)
        const data = await response.json();
        
        // Assume data is an array of completed examinees: [{ test_permit, name, email, exam_date }, ...]
        // If the structure differs, adjust accordingly.
        
        const tbody = document.getElementById('examineeTableBody');
        const currentRows = tbody.querySelectorAll('tr');
        const newRowCount = data.length;
        
        // Simple check: Update only if row count differs (or compute a hash for deeper comparison)
        if (currentRows.length !== newRowCount) {
            tbody.innerHTML = ''; // Clear existing rows
            data.forEach(examinee => {
                const row = `
                    <tr>
                        <td>${examinee.test_permit}</td>
                        <td>${examinee.name}</td>
                        <td>${examinee.email}</td>
                        <td>${examinee.exam_date}</td>
                    </tr>
                `;
                tbody.insertAdjacentHTML('beforeend', row);
            });
        }
    } catch (error) {
        console.error('Error fetching completed examinees:', error);
    }
}

// Initial load on page load
document.addEventListener('DOMContentLoaded', () => {
    fetchAndRenderCompletedExaminees();
    
    // Poll every 5 seconds for updates
    const pollInterval = setInterval(fetchAndRenderCompletedExaminees, 5000);
    
    // Stop polling on page unload to avoid memory leaks
    window.addEventListener('beforeunload', () => {
        clearInterval(pollInterval);
    });
});
