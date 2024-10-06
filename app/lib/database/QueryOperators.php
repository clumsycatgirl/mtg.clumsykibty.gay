<?php

namespace Lib\Database;

/**
 * Enum QueryOperators
 *
 * Represents SQL operators used in database queries.
 */
enum QueryOperators: string {
    case Equals = "=";                   // Equal to: =
    case NotEquals = "!=";               // Not equal to: !=
    case GreaterThan = ">";              // Greater than: >
    case LessThan = "<";                 // Less than: <
    case GreaterThanOrEquals = ">=";     // Greater than or equal to: >=
    case LessThanOrEquals = "<=";        // Less than or equal to: <=
    case Like = "LIKE";                  // Like (pattern matching): LIKE
    case NotLike = "NOT LIKE";           // Not like (pattern matching): NOT LIKE
    case In = "IN";                      // In a set of values: IN
    case NotIn = "NOT IN";               // Not in a set of values: NOT IN
    case Between = "BETWEEN";            // Between two values: BETWEEN
    case NotBetween = "NOT BETWEEN";     // Not between two values: NOT BETWEEN
    case IsNull = "IS NULL";             // Is null: IS NULL
    case IsNotNull = "IS NOT NULL";      // Is not null: IS NOT NULL
}
