const deleteTasklistForm = document.getElementById('delete-tasklist-form');

if(deleteTasklistForm){
    deleteTasklistForm.addEventListener('submit', function(e) {
        if (!confirm('Êtes-vous sûr de vouloir supprimer cette liste?')) {
            e.preventDefault();
        }
    });
}
