<?php

namespace Lib\Utils;

/**
 * Utility class to validate various types of data.
 */
final class Validator {
    private static ?Validator $instance = null;

    /**
     * Retrieves the singleton instance of Validator.
     *
     * @return Validator The singleton instance of Validator.
     */
    public static function get_instance(): Validator {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Validates a password against a specific pattern.
     *
     * This function validates a password against the specified pattern,
     * which requires at least one lowercase letter, one uppercase letter,
     * one digit, one special character, and a minimum length of 8 characters.
     *
     * @param string $password The password to validate.
     *
     * @return bool True if the password meets the validation criteria, false otherwise.
     */
    public function validate_password(string $password): bool {
        return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W])[A-Za-z\d\W]{8,}$/', $password);
    }

    /**
     * Validates an email address using PHP's built-in filter.
     *
     * This function validates an email address using PHP's FILTER_VALIDATE_EMAIL filter.
     *
     * @param string $email The email address to validate.
     *
     * @return bool True if the email address is valid, false otherwise.
     */
    public function validate_email(string $email): bool {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validates a phone number against a specific pattern.
     *
     * This function validates a phone number against the pattern, which requires exactly 10 digits.
     *
     * @param string $phone The phone number to validate.
     *
     * @return bool True if the phone number matches the validation pattern, false otherwise.
     */
    public function validate_phone(string $phone): bool {
        return preg_match('/^\d{10}$/', $phone);
    }

    /**
     * Validates a ZIP code against a specific pattern.
     *
     * This function validates a ZIP code against the pattern, which requires exactly 5 digits.
     *
     * @param string $zip The ZIP code to validate.
     *
     * @return bool True if the ZIP code matches the validation pattern, false otherwise.
     */
    public function validate_zip(string $zip): bool {
        return preg_match('/^\d{5}$/', $zip);
    }

    /**
     * Validates a URL using PHP's built-in filter.
     *
     * This function validates a URL using PHP's FILTER_VALIDATE_URL filter.
     *
     * @param string $url The URL to validate.
     *
     * @return bool True if the URL is valid, false otherwise.
     */
    public function validate_url(string $url): bool {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * Validates an IP address using PHP's built-in filter.
     *
     * This function validates an IP address using PHP's FILTER_VALIDATE_IP filter.
     *
     * @param string $ip The IP address to validate.
     *
     * @return bool True if the IP address is valid, false otherwise.
     */
    public function validate_ip(string $ip): bool {
        return filter_var($ip, FILTER_VALIDATE_IP) !== false;
    }

    /**
     * Validates a credit card number using the Luhn algorithm.
     *
     * This function checks if a credit card number is valid using the Luhn algorithm.
     *
     * @param string $credit_card_number The credit card number to validate.
     *
     * @return bool True if the credit card number is valid, false otherwise.
     */
    public function validate_credit_card(string $credit_card_number): bool {
        $credit_card_number = preg_replace('/[^0-9]/', '', $credit_card_number);
        if (strlen($credit_card_number) < 13 || strlen($credit_card_number) > 19) {
            return false;
        }

        $sum = 0;
        $length = strlen($credit_card_number);

        for ($i = 0; $i < $length; $i++) {
            $digit = (int) $credit_card_number[$length - $i - 1];
            if ($i % 2 === 1) {
                $digit *= 2;
                if ($digit > 9) {
                    $digit -= 9;
                }
            }
            $sum += $digit;
        }

        return $sum % 10 === 0;
    }
}
