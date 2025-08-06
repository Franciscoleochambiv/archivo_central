<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentoAdjunto extends Model
{
    use HasFactory;
    protected $table = 'documentos_adjuntos';

    protected $fillable = ['registro_documental_id', 'nombre', 'ruta'];

    public function registro()
    {
        return $this->belongsTo(RegistroDocumental::class, 'registro_documental_id');
    }
}
