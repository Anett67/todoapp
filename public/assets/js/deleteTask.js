const deleteTaskForm = document.getElementById('delete-task-form');
const deleteCompletedTaskForm = document.getElementById('delete-completed-task-form');

if(deleteTaskForm){
    deleteTaskForm.addEventListener('submit', function(e) {
        if (!confirm('Êtes-vous sûr de vouloir supprimer cette liste?')) {
            e.preventDefault();
        }
    });
}

if(deleteCompletedTaskForm){
    deleteCompletedTaskForm.addEventListener('submit', function(e) {
        if (!confirm('Êtes-vous sûr de vouloir supprimer cette liste?')) {
            e.preventDefault();
        }
    });
}
