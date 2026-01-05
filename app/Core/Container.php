<?php

class Container
{
    /**
     * Resolve the given class and its dependencies.
     */
    public function resolve($class)
    {
        // 1. Reflect on the class
        $reflector = new ReflectionClass($class);

        // 2. Check if instantiable
        if (!$reflector->isInstantiable()) {
            throw new Exception("Class {$class} is not instantiable");
        }

        // 3. Get Constructor
        $constructor = $reflector->getConstructor();

        // 4. If no constructor, return new instance
        if (is_null($constructor)) {
            return new $class;
        }

        // 5. Get Parameters
        $parameters = $constructor->getParameters();
        $dependencies = $this->getDependencies($parameters);

        // 6. Return new instance with dependencies
        return $reflector->newInstanceArgs($dependencies);
    }

    /**
     * Resolve dependencies recursively.
     */
    public function getDependencies($parameters)
    {
        $dependencies = [];

        foreach ($parameters as $parameter) {
            $type = $parameter->getType();

            if (is_null($type)) {
                // Non-typed parameter
                if ($parameter->isDefaultValueAvailable()) {
                    $dependencies[] = $parameter->getDefaultValue();
                } else {
                    throw new Exception("Cannot resolve class dependency {$parameter->name} - it has no type hint.");
                }
            } else {
                // Typed parameter (Dependency)
                // Assuming reflection is NamedType (PHP 7/8 standard for single types)
                if ($type instanceof ReflectionNamedType && !$type->isBuiltin()) {
                    $dependencies[] = $this->resolve($type->getName());
                } else {
                    // Primitives or built-ins without defaults
                    if ($parameter->isDefaultValueAvailable()) {
                        $dependencies[] = $parameter->getDefaultValue();
                    } else {
                        throw new Exception("Cannot resolve primitive dependency {$parameter->name}");
                    }
                }
            }
        }

        return $dependencies;
    }
}
