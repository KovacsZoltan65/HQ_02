<?php

namespace App\Repositories;

abstract class BaseRepository extends \Prettus\Repository\Eloquent\BaseRepository
{
    public function findWithoutFail($id, $columns = ['*'])
    {
        try
        {
            return $this->find($id, $columns);
        }
        catch( \Exception $e )
        {
            return;
        }
    }
    
    /**
     * Create a new resource.
     *
     * @param array $attributes
     * @return \App\Models\Model
     */
    public function create(array $attributes)
    {
        // Temporarily skip presenter to get a model without presenter data
        $temporarySkipPresenter = $this->skipPresenter;
        $this->skipPresenter(true);

        // Create a new model
        $model = parent::create($attributes);

        // Reset the presenter setting to its original value
        $this->skipPresenter($temporarySkipPresenter);

        // Update the model relations with the given attributes
        $model = $this->updateRelations($model, $attributes);

        // Save the model to the database
        $model->save();

        // Return the parsed result
        return $this->parserResult($model);
    }
    
    /**
     * Update a model with the given attributes.
     *
     * @param array $attributes The attributes to update
     * @param mixed $id The ID of the model to update
     * @throws Some_Exception_Class description of exception
     * @return Some_Return_Value
     */
    public function update(array $attributes, $id)
    {
        // Have to skip presenter to get a model not some data
        $temporarySkipPresenter = $this->skipPresenter;
        $this->skipPresenter(true);
        $model = parent::update($attributes, $id);
        $this->skipPresenter($temporarySkipPresenter);

        $model = $this->updateRelations($model, $attributes);
        $model->save();

        return $this->parserResult($model);
    }
    
    /**
     * Update the relations of the given model based on the provided attributes.
     *
     * @param mixed $model The model instance to update relations for
     * @param array $attributes The attributes to update the relations with
     * @return mixed The updated model instance
     */
    public function updateRelations($model, $attributes)
    {
        foreach ($attributes as $key => $val) {
            if (isset($model) &&
                method_exists($model, $key) &&
                is_a(@$model->$key(), 'Illuminate\Database\Eloquent\Relations\Relation')
            ) {
                $methodClass = get_class($model->$key($key));
                switch ($methodClass) {
                    case 'Illuminate\Database\Eloquent\Relations\BelongsToMany':
                        $new_values = array_get($attributes, $key, []);
                        if (array_search('', $new_values) !== false) {
                            unset($new_values[array_search('', $new_values)]);
                        }
                        $model->$key()->sync(array_values($new_values));
                        break;
                    case 'Illuminate\Database\Eloquent\Relations\BelongsTo':
                        $model_key = $model->$key()->getForeignKey();
                        $new_value = array_get($attributes, $key, null);
                        $new_value = $new_value == '' ? null : $new_value;
                        $model->$model_key = $new_value;
                        break;
                    case 'Illuminate\Database\Eloquent\Relations\HasOne':
                        break;
                    case 'Illuminate\Database\Eloquent\Relations\HasOneOrMany':
                        break;
                    case 'Illuminate\Database\Eloquent\Relations\HasMany':
                        $new_values = array_get($attributes, $key, []);
                        if (array_search('', $new_values) !== false) {
                            unset($new_values[array_search('', $new_values)]);
                        }

                        list($temp, $model_key) = explode('.', $model->$key($key)->getForeignKey());

                        foreach ($model->$key as $rel) {
                            if (!in_array($rel->id, $new_values)) {
                                $rel->$model_key = null;
                                $rel->save();
                            }
                            unset($new_values[array_search($rel->id, $new_values)]);
                        }

                        if (count($new_values) > 0) {
                            $related = get_class($model->$key()->getRelated());
                            foreach ($new_values as $val) {
                                $rel = $related::find($val);
                                $rel->$model_key = $model->id;
                                $rel->save();
                            }
                        }
                        break;
                }
            }
        }

        return $model;
    }
    
    /**
     * Applies the criteria and scope to the model, including soft-deleted records.
     *
     * @param datatype $paramname description
     * @throws Some_Exception_Class description of exception
     * @return Some_Return_Value
     */
    public function withTrashed()
    {
        // Apply the defined criteria to the model
        $this->applyCriteria();

        // Apply the defined scope to the model
        $this->applyScope();

        // Retrieve all records, including soft-deleted ones
        $result = $this->model->withTrashed();

        // Reset the model instance
        $this->resetModel();

        // Reset the scope
        $this->resetScope();

        // Parse and return the result
        return $this->parserResult($result);
    }
    
    /**
     * Retrieve only the trashed items from the model.
     *
     * @return Some_Return_Value
     */
    public function onlyTrashed()
    {
        // Apply any relevant criteria
        $this->applyCriteria();

        // Apply any relevant scope
        $this->applyScope();

        // Retrieve only the trashed items from the model
        $results = $this->model->onlyTrashed();

        // Reset the model to its default state
        $this->resetModel();

        // Reset the scope to its default state
        $this->resetScope();

        // Parse and return the results
        return  $this->parserResult($results);
    }
    
    /**
     * The where function applies criteria and scope, performs a where query on the model, 
     * and then resets the model and scope before returning the parsed results.
     *
     * @param datatype $column description of column
     * @param datatype $operator description of operator
     * @param datatype $value description of value
     * @return Some_Return_Value description of return value
     */
    public function where($column, $operator, $value)
    {
        // Apply any set criteria
        $this->applyCriteria();

        // Apply any set scope
        $this->applyScope();

        // Add the "OR" condition to the query
        $results = $this->model->where($column, $operator, $value);
        
        // Reset the model instance to the default state
        $this->resetModel();

        // Reset any set scope
        $this->resetScope();

        // Parse and return the query results
        return $this->parserResult($results);
    }
    
    public function orWhere($column, $operator, $value)
    {
        $this->applyCriteria();
        $this->applyScope();
        
        $results = $this->model->orWhere($column, $operator, $value);
        
        $this->resetModel();
        $this->resetScope();

        return $this->parserResult($results);
    }
    
    /* Perform a join operation on the model's query builder.
     *
     * @param string $table The table to join
     * @param string $first The first column to join on
     * @param string|null $operator The operator for the join
     * @param string|null $second The second column to join on
     * @param string $type The type of join (e.g. inner, left, right)
     * @param bool $where Whether the join is a "where" type join
     * @return mixed The results of the join operation
     */
    public function join($table, $first, $operator = null, $second = null, $type = 'inner', $where = false)
    {
        // Apply any criteria set on the model's query builder
        $this->applyCriteria();

        // Apply any scope set on the model's query builder
        $this->applyScope();

        // Perform the join operation and retrieve the results
        $results = $this->model->join($table, $first, $operator, $second, $type, $where);

        // Reset the model's query builder state after the join operation
        $this->resetModel();

        // Reset the model's query builder scope after the join operation
        $this->resetScope();

        // Parse and return the results of the join operation
        return $this->parserResult($results);
    }
}