

namespace App\Models;
class Task extends Model
{
    protected $fillable = ['title','status','deadline','project_id','attachment_path'];

    public function project() { return $this->belongsTo(Project::class); }

    // Accessor US6
    public function getStatusLabelAttribute() {
        return match($this->status){
            'pending'=>'En attente',
            'in_progress'=>'En cours',
            'done'=>'Terminé',
        };
    }

    // Scope US6
    public function scopeOverdue($query){
        return $query->where('deadline','<',now())->where('status','!=','done');
    }
}
