<?php

namespace Lib;

/**
 * Enum representing HTTP request methods.
 */
enum RequestMethod: string {
    /** Represents the GET HTTP method */
    case GET = 'GET';
    /** Represents the POST HTTP method */
    case POST = 'POST';
    // The following methods are currently marked as UNUSED.
    // case PUT = 'PUT'; // UNUSED: Represents the PUT HTTP method.
    // case DELETE = 'DELETE'; // UNUSED: Represents the DELETE HTTP method.

    /**
     * Converts the enum value to its string representation.
     *
     * @return string The string representation of the enum value.
     */
    public function to_string(): string {
        return $this->value;
    }
}

