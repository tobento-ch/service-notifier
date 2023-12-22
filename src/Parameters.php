<?php

/**
 * TOBENTO
 *
 * @copyright   Tobias Strub, TOBENTO
 * @license     MIT License, see LICENSE file distributed with this source code.
 * @author      Tobias Strub
 * @link        https://www.tobento.ch
 */

declare(strict_types=1);

namespace Tobento\Service\Notifier;

use ArrayIterator;
use Traversable;

/**
 * Parameters
 */
class Parameters implements ParametersInterface
{
    /**
     * @var array<int, ParameterInterface>
     */
    protected array $parameters = [];
    
    /**
     * Create a new Parameters.
     *
     * @param ParameterInterface ...$parameter
     */
    public function __construct(
        ParameterInterface ...$parameter,
    ) {
        $this->parameters = $parameter;
    }

    /**
     * Add a new parameter.
     *
     * @param ParameterInterface $parameter
     * @return static $this
     */
    public function add(ParameterInterface $parameter): static
    {
        $this->parameters[] = $parameter;
        
        return $this;
    }
    
    /**
     * Remove a parameter.
     *
     * @param string $name
     * @return static $this
     */
    public function remove(string $name): static
    {
        $this->parameters = $this->filter(
            fn(ParameterInterface $p): bool => $p->getName() !== $name
        )->all();
        
        return $this;
    }
    
    /**
     * Returns a new instance with the filtered parameters.
     *
     * @param callable $callback
     * @return static
     */
    public function filter(callable $callback): static
    {
        $new = clone $this;
        $new->parameters = array_filter($this->parameters, $callback);
        return $new;
    }
    
    /**
     * Returns a new instance with the filtered parameters by name.
     *
     * @param string $name
     * @return static
     */
    public function name(string $name): static
    {
        return $this->filter(fn(ParameterInterface $p): bool => $p->getName() === $name);
    }
    
    /**
     * Returns the first parameter of null if none.
     *
     * @return null|object
     */
    public function first(): null|object
    {
        $key = array_key_first($this->parameters);
        
        if (is_null($key)) {
            return null;
        }
        
        return $this->parameters[$key];    
    }
    
    /**
     * Returns the parameters.
     *
     * @return array<int, ParameterInterface>
     */
    public function all(): array
    {
        return $this->parameters;
    }
    
    /**
     * Get the iterator. 
     *
     * @return Traversable
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->all());
    }
}