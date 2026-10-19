import Pluralize from 'pluralize';

/**
 * Define the global formatting helpers that mirror the PHP ones: money, numeric, parseNumber, pluralize,
 * prettify, formatDate and formatDateTime. The currency comes from the `data-currency` attribute that the
 * layout sets on the `<html>` element, which holds `essentials.currency`.
 */

/**
 * Format an amount as money in the app's currency
 */
window.money = (amount = 0) => {
    //  Get the currency from the layout
    const currency = document.documentElement.dataset.currency || 'KES';

    //  Convert the amount to a numeric value
    const number = Number(amount);

    //  Return the original amount if it is not a valid number
    if (isNaN(number)) return amount;

    //  Format the value as a currency
    return number.toLocaleString('en-US', { style: 'currency', currency, currencyDisplay: 'code' });
};

/**
 * Format an amount with thousands separators and a fixed number of decimals
 */
window.numeric = (amount = 0, decimals = 0) => {
    //  Convert the amount to a numeric value
    const number = Number(amount);

    //  Format the value, or return the original amount if it is not a valid number
    return !isNaN(number)
        ? number.toLocaleString('en-US', { minimumFractionDigits: decimals, maximumFractionDigits: decimals })
        : amount;
};

/**
 * Turn a formatted number such as "1,200.50" back into a float
 */
window.parseNumber = (number = null) => {
    //  Check if the number is null or undefined
    if (number === null || number === undefined) return null;

    //  Remove commas and convert to float
    return parseFloat(String(number).replace(/,/g, ''));
};

/**
 * Pluralize a word for a count
 */
window.pluralize = (word, count, inclusive = false) => Pluralize(word, count, inclusive);

/**
 * Turn "some_snake-case" into "Some Snake Case"
 */
window.prettify = (string) => {
    //  Return an empty string when there is nothing to prettify
    if (!string) return '';

    //  Replace underscores and hyphens with spaces
    const spaced = string.replace(/[_-]+/g, ' ');

    //  Capitalize the first letter of each word
    return spaced.replace(/\b\w/g, (char) => char.toUpperCase());
};

/**
 * Format a date as "12 Mar 2026"
 */
window.formatDate = (date) => {
    //  Check if the date is provided
    if (!date) return 'Not provided';

    //  Parse the date
    const parsed = new Date(date);

    //  Return a message for a date that cannot be parsed
    if (isNaN(parsed)) return 'Invalid date';

    //  Format the date
    return parsed.toLocaleDateString('en-KE', { year: 'numeric', month: 'short', day: 'numeric' });
};

/**
 * Format a date and time as "Thu, 12 Mar 2026, 02:30 pm"
 */
window.formatDateTime = (datetime) => {
    //  Check if the datetime is provided
    if (!datetime) return 'Not specified';

    //  Parse the datetime
    const parsed = new Date(datetime);

    //  Return a message for a datetime that cannot be parsed
    if (isNaN(parsed)) return 'Invalid date';

    //  Format the datetime
    return parsed.toLocaleString('en-KE', {
        weekday: 'short',
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};
