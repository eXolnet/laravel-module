<{{ '?php' }} namespace {{ $namespace  }};

use Illuminate\Database\Eloquent\Model;

class {{ $name }} extends Model {

@if ($primaryKey !== 'id')
    /**
     * @var string
     */
    protected $primaryKey = '{{ $primaryKey }}';

@endif
    /**
     * @var string
     */
    protected $table = '{{ $tableName }}';

    /**
     * @var array
     */
    protected $fillable = [{!! $fillableColumns !!}];

@if ($disableTimestamps)
    /**
     * @var boolean
     */
    public $timestamps = false;

@endif
}
