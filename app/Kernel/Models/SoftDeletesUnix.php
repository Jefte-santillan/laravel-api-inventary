<?php

namespace App\Kernel\Models;

use Illuminate\Database\Eloquent as EloquentBase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

trait SoftDeletesUnix
{

    use EloquentBase\SoftDeletes;

    /**
     * The storage format of the model's SoftDelete column.
     *
     * @var string
     */
    protected $softDeleteDateFormat = 'U';

    /**
     * Initialize the soft deleting trait for an instance.
     *
     * @return void
     */
    public function initializeSoftDeletes()
    {
        if (!isset($this->casts[$this->getDeletedAtColumn()])) {
            $this->casts[$this->getDeletedAtColumn()] = 'integer';
        }
    }

    /**
     * Boot the soft deleting trait for a model.
     *
     * @return void
     */
    public static function bootSoftDeletes()
    {
        static::addGlobalScope(new SoftDeletesUnixScope);
    }

    /**
     * Perform the actual delete query on this model instance.
     *
     * @return void
     */
    protected function runSoftDelete()
    {
        $query = $this->setKeysForSaveQuery($this->newModelQuery());
        $time = $this->freshTimestamp();
        $columns = [$this->getDeletedAtColumn() => $this->freshTimestampUnix($time)];
        $this->{$this->getDeletedAtColumn()} = $time;
        if ($this->timestamps && !is_null($this->getUpdatedAtColumn())) {
            $this->{$this->getUpdatedAtColumn()} = $time;
            $columns[$this->getUpdatedAtColumn()] = $this->fromDateTime($time);
        }
        $query->update($columns);
    }

    /**
     * Get a fresh timestamp for the model.
     *
     * @return int|string
     */
    public function freshTimestampUnix($time = null)
    {
        $now = isset($time) ? $time : $this->freshTimestamp();
        $fmt = $this->softDeleteDateFormat ?: $this->getDateFormat();
        return empty($now) ? $now : $this->asDateTime($now)->format($fmt);
    }

    /**
     * Instantiate a new HasManyThrough relationship.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Database\Eloquent\Model $farParent
     * @param \Illuminate\Database\Eloquent\Model $throughParent
     * @param string $firstKey
     * @param string $secondKey
     * @param string $localKey
     * @param string $secondLocalKey
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     * @version 5.5+
     *
     */
    protected function newHasManyThrough(Builder $query, Model $farParent, Model $throughParent, $firstKey, $secondKey, $localKey, $secondLocalKey)
    {
        return new SoftDeletesUnixHasManyThrought($query, $farParent, $throughParent, $firstKey, $secondKey, $localKey, $secondLocalKey);
    }

    /**
     * Restores the model from deletion.
     *
     * First, it fires the 'restoring' event. If the event handler does not return false,
     * it proceeds to reset the deleted_at attribute to 0, then saves the model and fires
     * the 'restored' event. This method returns the result of the save operation.
     *
     * @return bool|null False if the 'restoring' event handler returns false. Otherwise, the result of the save operation.
     */
    public function restore()
    {
        if ($this->fireModelEvent('restoring') === false) {
            return false;
        }

        $this->{$this->getDeletedAtColumn()} = 0;

        $this->exists = true;

        $result = $this->save();

        $this->fireModelEvent('restored', false);

        return $result;
    }

    /**
     * Checks if the model instance has been soft-deleted.
     *
     * This is determined by checking if the deleted_at attribute has a value.
     *
     * @return mixed The value of the deleted_at attribute.
     */
    public function trashed()
    {
        return $this->{$this->getDeletedAtColumn()};
    }
}

