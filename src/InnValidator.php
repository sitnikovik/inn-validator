<?php

namespace Sitnikovik\InnValidator;

/**
 * Class to validate INN (called ITN in english)
 *
 * @link https://ru.wikipedia.org/wiki/%D0%9A%D0%BE%D0%BD%D1%82%D1%80%D0%BE%D0%BB%D1%8C%D0%BD%D0%BE%D0%B5_%D1%87%D0%B8%D1%81%D0%BB%D0%BE#%D0%9D%D0%BE%D0%BC%D0%B5%D1%80%D0%B0_%D0%98%D0%9D%D0%9D Algorithm described on Wikipedia
 */
final class InnValidator
{
    /**
     * Coefficients for the control number n2 for ITN of private persons and individual entrepreneurs.
     */
    private const PRIVATE_PERSONS_N2_COEFFICIENTS = [7, 2, 4, 10, 3, 5, 9, 4, 6, 8, 0];

    /**
     * Coefficients for the control number n1 for ITN of private persons and individual entrepreneurs.
     */
    private const PRIVATE_PERSONS_N1_COEFFICIENTS = [3, 7, 2, 4, 10, 3, 5, 9, 4, 6, 8, 0];

    /**
     * Coefficients for the control number n1 for organizations.
     */
    private const ORGANIZATIONS_COEFFICIENTS = [2, 4, 10, 3, 5, 9, 4, 6, 8, 0];

    /**
     * Index from the end of the ITN line for control number n1.
     *
     * The first number in the ITN is taken from the end of the line.
     */
    private const N1_INDEX = -1;

    /**
     * Index from the end of the ITN line for control number n2.
     *
     * The second number in the ITN is taken from the end of the line.
     * Used only for ITN of private persons and individual entrepreneurs.
     */
    private const N2_INDEX = -2;

    /**
     * For calculating check numbers.
     */
    private const CALC_CONSTANT = 11;

    /**
     * Defines if the ITN is correct.
     * ---
     *
     * The essence of checking the results is in comparing the control numbers at the end of the ITN.
     * For individuals and individual entrepreneurs, the last two numbers are compared,
     * and for organizations - the last one.
     *
     * Algorithm:
     *
     * 1) Calculation of the checksum from the ITN by the coefficients.
     *
     * 2) Calculation of control numbers by checksum.
     *
     * 3) Approval of sensitivity of control numbers for ITN.
     *
     * @link https://ru.wikipedia.org/wiki/%D0%9A%D0%BE%D0%BD%D1%82%D1%80%D0%BE%D0%BB%D1%8C%D0%BD%D0%BE%D0%B5_%D1%87%D0%B8%D1%81%D0%BB%D0%BE#%D0%9D%D0%BE%D0%BC%D0%B5%D1%80%D0%B0_%D0%98%D0%9D%D0%9D Algorithm described on Wikipedia
     *
     * @param string $inn
     * @return bool
     */
    public static function validate(string $inn): bool
    {
        $inn = trim($inn);

        if (self::isForPrivatePersons($inn)) {

            $n1Checksum = self::calcChecksumForCheckDigit($inn, self::N1_INDEX);
            $n2Checksum = self::calcChecksumForCheckDigit($inn, self::N2_INDEX);

            $n1 = self::calcCheckDigit($n1Checksum);
            $n2 = self::calcCheckDigit($n2Checksum);

            return ((int)$inn[10] === $n2 && (int)$inn[11] === $n1);
        }

        if (self::isForOrganizations($inn)) {
            $n1Checksum = self::calcChecksumForCheckDigit($inn, self::N1_INDEX);

            $n1 = self::calcCheckDigit($n1Checksum);

            return (int)$inn[9] === $n1;
        }

        return false;
    }

    /**
     * Defines, if ITN is for organizations.
     *
     * @param string $inn
     * @return bool
     */
    private static function isForOrganizations(string $inn): bool
    {
        return strlen($inn) === 10;
    }

    /**
     * Defines, if ITN is for individuals.
     *
     * @param string $inn
     * @return bool
     */
    private static function isForPrivatePersons(string $inn): bool
    {
        return strlen($inn) === 12;
    }

    /**
     * Calculates the checksum for the control number N by its index.
     *
     * Selects coefficients according to the length of the ITN line.
     *
     * @param string $inn
     * @param int $nIndex
     * @return int
     */
    private static function calcChecksumForCheckDigit(string $inn, int $nIndex): int
    {
        $innDigits = self::splitInnUpToIndex($inn, $nIndex);
        $coefficients = self::getCoefficients($inn, $nIndex);

        return self::calcCheckSum($innDigits, $coefficients);
    }

    /**
     * Explode INN to array of int.
     *
     * Returns the numbers from the start of ITN to `$length`.
     *
     * @param string $inn
     * @param int $length
     * @return int[]
     */
    private static function splitInnUpToIndex(string $inn, int $length): array
    {
        return array_map('intval', str_split(substr($inn, 0, $length)));
    }

    /**
     * Selects coefficients for ITN under the index of the control number.
     *
     * Depending on the type of taxable person and the index of the control number, the coefficients will be different.
     *
     * @param string $inn
     * @param int $nIndex The index of the control number, under which the coefficients are selected
     * @return int[]
     */
    private static function getCoefficients(string $inn, int $nIndex): array
    {
        if (self::isForPrivatePersons($inn)) {
            return ($nIndex === self::N2_INDEX)
                ? self::PRIVATE_PERSONS_N2_COEFFICIENTS
                : self::PRIVATE_PERSONS_N1_COEFFICIENTS;
        }

        return self::ORGANIZATIONS_COEFFICIENTS;
    }

    /**
     * Calculates the checksum for the check number N.
     *
     * @param array $innDigits ITN numbers to be multiplied by coefficients
     * @param array $coefficients Odds grouped by digit indexes in `$innDigits`
     * @return int
     */
    private static function calcCheckSum(array $innDigits, array $coefficients): int
    {
        $result = 0;
        foreach ($innDigits as $i => $digit) {
            $result += $digit * $coefficients[$i];
        }

        return $result;
    }

    /**
     * Calculates the control number N.
     *
     * @param int $checksum
     * @return int
     */
    private static function calcCheckDigit(int $checksum): int
    {
        $n = $checksum % self::CALC_CONSTANT;

        return $n > 9 ? 0 : $n;
    }
}