import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { toast } from 'vue3-toastify'
import 'vue3-toastify/dist/index.css'
import Cleave from 'cleave.js'

export default function ReusableFunctions() {
    const randomNumber = ref(0)
    const slug = ref('')
    const selectedFile = ref('null')
    const router = useRouter()

    const isObjectEmpty = (obj) => {
        // Check if obj is null or undefined
        if (obj === null || obj === undefined) {
            return true
        }

        if (obj === '{}') {
            return true
        }

        // Check if obj is not empty
        if (Object.keys(obj).length > 0) {
            for (const key in obj) {
                if (!obj[key]) {
                    return false
                }
            }
            return Object.keys(obj).length === 0 && obj.constructor === Object
        } else {
            return false // Return false if obj is empty
        }
    }

    const isEmptyArray = (array) => {
        return array === null || array === undefined || (Array.isArray(array) && array.length === 0)
    }

    const countArrayItems = (array) => {
        if (isEmptyArray(array)) {
            return 0
        }
        return array.length
    }

    const generateRandomNumber = () => {
        return (randomNumber.value = Math.floor(Math.random() * 2))
    }

    const truncate = (string, clip) => {
        return string.length > clip ? string.slice(0, clip - 1) + '...' : string
    }

    const handleFileChange = (event) => {
        selectedFile.value = event.target.files[0]
    }

    const isDateBeforeToday = (compareDate) => {
        const today = new Date()
        const compare = new Date(compareDate)

        today.setHours(0, 0, 0, 0)
        compare.setHours(0, 0, 0, 0)

        return today > compare
    }

    const currentDate = () => {
        const today = new Date()
        const dd = String(today.getDate()).padStart(2, '0')
        const mm = String(today.getMonth() + 1).padStart(2, '0')
        const yyyy = today.getFullYear()

        return yyyy + '-' + mm + '-' + dd
    }

    const useReusableNavigation = (isUrl, route, props = null) => {
        if (isUrl) {
            return router.push(route)
        }
        return router.push({ name: route, params: props })
    }

    const capitalizeFirstLetter = (word) => {
        if (word != undefined && word.length > 0) {
            return word.charAt(0).toUpperCase() + word.slice(1)
        }
        return ''
    }

    const convertToSlug = (text) => {
        if (!text.trim()) {
            return ''
        }
        return text
            .toLowerCase()
            .replace(/[^\w\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/--+/g, '-')
            .trim()
    }

    const getSlug = (text) => {
        return (slug.value = convertToSlug(text))
    }

    const updateApiHeader = (isJson = true) => {
        return {
            headers: {
                'Content-Type': isJson ? 'application/json' : 'multipart/form-data',
                _method: 'PUT',
                'X-HTTP-Method-Override': 'PUT'
            }
        }
    }

    const createApiHeader = (isJson = true) => {
        return {
            headers: {
                'Content-Type': isJson ? 'application/json' : 'multipart/form-data',
                _method: 'POST'
            }
        }
    }

    const patchApiHeader = (isJson = true) => {
        return {
            headers: {
                'Content-Type': isJson ? 'application/json' : 'multipart/form-data',
                _method: 'PATCH',
                'X-HTTP-Method-Override': 'PATCH'
            }
        }
    }

    const getDaySuffix = (day) => {
        if (day >= 11 && day <= 13) {
            return 'th'
        }

        const lastDigit = day % 10
        switch (lastDigit) {
            case 1:
                return 'st'
            case 2:
                return 'nd'
            case 3:
                return 'rd'
            default:
                return 'th'
        }
    }

    const humanReadableDate = (dateOfBirth) => {
        const months = [
            'January',
            'February',
            'March',
            'April',
            'May',
            'June',
            'July',
            'August',
            'September',
            'October',
            'November',
            'December'
        ]

        const daysOfWeek = [
            'Sunday',
            'Monday',
            'Tuesday',
            'Wednesday',
            'Thursday',
            'Friday',
            'Saturday'
        ]

        const dob = new Date(dateOfBirth)
        const day = dob.getDate()
        const month = months[dob.getMonth()]
        const year = dob.getFullYear()
        const dayOfWeek = daysOfWeek[dob.getDay()]

        const suffix = getDaySuffix(day)

        return `${dayOfWeek} ${day}${suffix} ${month} ${year}`
    }

    const generateUUID = () => {
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
            const r = (Math.random() * 16) | 0
            const v = c === 'x' ? r : (r & 0x3) | 0x8
            return v.toString(16)
        })
    }

    const getAssetPath = (path) => {
        return import.meta.env.BASE_URL + path
    }

    const handleImageError = (event) => {
        return (event.target.src = getAssetPath('images/favicon.svg'))
    }

    const toastAlert = (message = null, type = 'error') => {
        return type === 'success' ? toast.success(message) : toast.error(message)
    }

    const hasMixedCharacters = (str) => {
        const hasLowercase = /[a-z]/.test(str)
        const hasUppercase = /[A-Z]/.test(str)
        const hasNumber = /\d/.test(str) // Checks for at least one digit
        const hasSymbol = /[!@#$%^&*(),.?":{}|<>]/.test(str) // check for symbols

        return hasLowercase && hasUppercase && hasNumber && hasSymbol
    }

    const isWordInArray = (word, wordArray) => {
        const lowercaseWord = word.toLowerCase()
        const lowercaseArray = wordArray.map((w) => w.toLowerCase())
        return lowercaseArray.includes(lowercaseWord)
    }

    const createScript = (url) => {
        // Load external script dynamically
        const script = document.createElement('script')
        script.src = url
        script.async = true
        document.head.appendChild(script)
    }

    const checkPassword = (password) => {
        const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#])[A-Za-z\d@$!%*?&#]{8,}$/

        if (!passwordRegex.test(password)) {
            return false
        } else {
            return true
        }
    }

    const maskEmail = (email) => {
        if (!email) return ''

        if (email.includes('@')) {
            const [username, domain] = email.split('@')
            const maskedUsername =
                username.charAt(0) + '*'.repeat(username.length - 2) + username.charAt(username.length - 1)
            return maskedUsername + '@' + domain
        } else {
            return email
        }
    }

    const formatNumber = (value) => {
        // Replace non-numeric characters and leading zeros
        let numericValue = value.replace(/[^0-9.-]/g, '').replace(/^0+/, '')

        // Strip extra decimal separators
        numericValue = stripExtraDots(numericValue)

        // Separate integer and decimal parts
        const [integerPart, decimalPart] = numericValue.split('.')

        // Format the integer part to thousands
        const formattedIntegerPart = integerPart.replace(/\d(?=(\d{3})+$)/g, '$&,')

        // If decimal part is present, limit it to two places; otherwise, append .00
        const formattedDecimalPart = decimalPart ? `.${decimalPart.slice(0, 2)}` : '.00'

        // Combine the formatted integer and decimal parts
        const formattedValue = `${formattedIntegerPart}${formattedDecimalPart}`

        return formattedValue
    }

    const formatNumberwithComma = (value) => {
        // Check if the value is defined and not null
        if (value === undefined || value === null) {
            return 'Invalid number'
        }

        // Convert the value to string and remove any commas
        const sanitizedValue = String(value).replace(/,/g, '')

        return Number(sanitizedValue).toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        })
    }

    const formatDecimalNumber = (value) => {
        // if (value.length > 0) {
        return parseFloat(value).toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        })
        // }

        // return '0.00'

        // return parseFloat(value.replace(/[^\d.]/g, '')).toLocaleString('en-US', {
        //   minimumFractionDigits: 2,
        //   maximumFractionDigits: 2
        // })
    }

    const stripExtraDots = (value) => {
        const dotCount = value.split('.').length - 1

        if (dotCount >= 2) {
            const parts = value.split('.')
            parts.pop() // Remove the last part after the last dot
            return parts.join('.')
        }

        return value
    }

    const unformatNumber = (value) => {
        if (value.length > 0) {
            return parseFloat(value.replace(/,/g, ''))
        }
    }

    const getInitials = (name) => {
        const words = name.split(' ')
        const initials = words.map((word) => word.charAt(0).toUpperCase())
        return initials.join('')
    }

    const acceptNumberOnly = (event) => {
        const keyCode = event.keyCode

        // Allow only numeric characters (0-9), backspace, delete, left arrow, and right arrow
        if (
            !(
                (keyCode >= 48 && keyCode <= 57) ||
                keyCode === 8 ||
                keyCode === 46 ||
                keyCode === 37 ||
                keyCode === 39
            )
        ) {
            event.preventDefault()
        }
    }

    const acceptAmountField = (event) => {
        const keyCode = event.keyCode
        const inputChar = String.fromCharCode(keyCode)

        // Allow only numeric characters (0-9) and backspace
        if (!/[\d\b]/.test(inputChar)) {
            event.preventDefault()
        }

        // const keyCode = event.keyCode;

        // // Allow only numeric characters (0-9), backspace, and decimal point
        // if (!((keyCode >= 48 && keyCode <= 57) || keyCode === 8 || keyCode === 46)) {
        //     event.preventDefault();
        // }
    }

    const isValidPhoneNumber = (phoneNumber) => {
        // Check if the phone number is null or empty
        if (!phoneNumber || phoneNumber === '') {
            return false
        }

        // Regex pattern to match Nigerian phone numbers
        const regex = /^(070|080|091|090|081|071)\d{8}$/

        // Check if the phone number matches the regex pattern
        return regex.test(phoneNumber)
    }

    const isValidSmartcardNumber = (smartcardNumber) => {
        const smartcardString = String(smartcardNumber)

        // Check if the smartcard number is null or empty
        if (!smartcardString || smartcardString === '') {
            return false
        }

        // Check if the smartcard number length is either 10 or 11
        if (smartcardString.length === 10 || smartcardString.length === 11) {
            return true
        }

        return false
    }

    const isValidMeterNumber = (meterNumber) => {
        const smartcardString = String(meterNumber)

        // Check if the smartcard number is null or empty
        if (!smartcardString || smartcardString === '') {
            return false
        }

        // Check if the smartcard number length is either 10 or 11
        if (smartcardString.length === 11) {
            return true
        }

        return false
    }

    const saveToLocalStore = (key, value) => {
        window.localStorage.setItem(key, value)

        return true
    }

    const getFromLocalStore = (key) => {
        return JSON.parse(window.localStorage.getItem(key))
    }

    const destroyToken = (key) => {
        window.localStorage.removeItem(key)
    }

    const hideBalance = (value) => {
        if (getFromLocalStore('show_balance') === true) {
            return value
        }

        return '*******'
    }

    const numberFormatter = (id) => {
        return new Cleave(`#${id}`, {
            numeral: true,
            numeralThousandsGroupStyle: 'thousand'
        })
    }

    const stringWithoutSpaces = (string) => {
        let stringWithoutSpaces = ''
        for (let i = 0; i < string.length; i++) {
            if (string[i] !== ' ') {
                stringWithoutSpaces += string[i]
            }
        }

        return stringWithoutSpaces
    }

    const asianCountries = () => {
        return [
            'INR',
            'NPR',
            'PHP',
            'BDT',
            'CNY',
            'KRW',
            'LKR',
            'NZD',
            'IDR',
            'MYR',
            'THB',
            'SGD',
            'HKD',
            'VND',
            'JPY',
            'PKR',
            'AED'
        ]
    }

    const asianIpayCodes = () => {
        return [
            'IPAYINR',
            'IPAYNPR',
            'IPAYPHP',
            'IPAYBDT',
            'IPAYCNY',
            'IPAYKRW',
            'IPAYLKR',
            'IPAYNZD',
            'IPAYIDR',
            'IPAYMYR',
            'IPAYTHB',
            'IPAYSGD',
            'IPAYHKD',
            'IPAYVND',
            'IPAYJPY',
            'IPAYPKR',
            'IPAYAED'
        ]
    }

    const allIpayCodes = () => {
        return [
            'IPAYINR',
            'IPAYNPR',
            'IPAYPHP',
            'IPAYBDT',
            'IPAYCNY',
            'IPAYKRW',
            'IPAYLKR',
            'IPAYNZD',
            'IPAYIDR',
            'IPAYMYR',
            'IPAYTHB',
            'IPAYSGD',
            'IPAYHKD',
            'IPAYVND',
            'IPAYJPY',
            'IPAYPKR',
            'IPAYAED',
            'IPAYEUR',
            'IPAYGBP'
        ]
    }

    const mobileMoneyCountries = () => {
        return ['XOF', 'XAF', 'GHS', 'GNF', 'KES', 'MWK', 'MZN', 'TZS', 'UGX', 'ZMW', 'ZWL']
    }

    const subtractYearsFromCurrentYear = (year = 18) => {
        const today = new Date()
        const maxYear = today.getFullYear() - year
        return maxYear
    }

    const sum = (num1, num2) => {
        const precision = Math.max(
            num1.toString().split('.')[1]?.length ?? 0,
            num2.toString().split('.')[1]?.length ?? 0
        )
        const multiplier = Math.pow(10, precision)
        return (Math.round(num1 * multiplier) + Math.round(num2 * multiplier)) / multiplier
    }

    const ipayRecipientCountryName = (currencyCode) => {
        const countries = {
            AUT: 'AUSTRIA',
            ALB: 'ALBANIA',
            AND: 'ANDORRA',
            ARM: 'ARMENIA',
            AZE: 'AZERBAIJAN',
            BEL: 'BELGIUM',
            BIH: 'BOSNIA AND HERZEGOVINA',
            BGR: 'BULGARIA',
            BLR: 'BELARUS',
            CHE: 'SWITZERLAND',
            CYP: 'CYPRUS',
            CZE: 'CZECH REPUBLIC',
            DEU: 'GERMANY',
            DNK: 'DENMARK',
            ESP: 'SPAIN',
            EST: 'ESTONIA',
            FRO: 'FAROE ISLANDS',
            FIN: 'FINLAND',
            FRA: 'FRANCE',
            GBR: 'UNITED KINGDOM',
            GEO: 'GEORGIA',
            GRC: 'GREECE',
            HUN: 'HUNGARY',
            ISL: 'ICELAND',
            IRL: 'IRELAND',
            ITA: 'ITALY',
            KAZ: 'KAZAKHSTAN',
            LIE: 'LIECHTENSTEIN',
            LTU: 'LITHUANIA',
            LUX: 'LUXEMBOURG',
            MDA: 'MOLDOVA',
            MCO: 'MONACO',
            MNE: 'MONTENEGRO',
            NLD: 'NETHERLANDS',
            NOR: 'NORWAY',
            POL: 'POLAND',
            PRT: 'PORTUGAL',
            ROM: 'ROMANIA',
            RUS: 'RUSSIA',
            SMR: 'SAN MARINO',
            SRB: 'SERBIA',
            SVK: 'SLOVAKIA',
            SVN: 'SLOVENIA',
            SWE: 'SWEDEN',
            TUR: 'TURKEY',
            UKR: 'UKRAINE',
            VAT: 'VATICAN CITY',
            IND: 'INDIA',
            INR: 'INDIA',
            NPR: 'NEPAL',
            NPL: 'NEPAL',
            PHP: 'PHILIPPINES',
            PHL: 'PHILIPPINES',
            BDT: 'BANGLADESH',
            BGD: 'BANGLADESH',
            CNY: 'CHINA',
            CHN: 'CHINA',
            KRW: 'SOUTH KOREA',
            KOR: 'SOUTH KOREA',
            LKR: 'SRI LANKA',
            LKA: 'SRI LANKA',
            NZD: 'NEW ZEALAND',
            NZL: 'NEW ZEALAND',
            IDR: 'INDONESIA',
            IDN: 'INDONESIA',
            MYR: 'MALAYSIA',
            MYS: 'MALAYSIA',
            THA: 'THAILAND',
            THB: 'THAILAND',
            SGD: 'SINGAPORE',
            SGP: 'SINGAPORE',
            HKD: 'HONG KONG',
            HKG: 'HONG KONG',
            VND: 'VIETNAM',
            VNM: 'VIETNAM',
            JPN: 'JAPAN',
            JPY: 'JAPAN',
            PKR: 'PAKISTAN',
            PAK: 'PAKISTAN',
            AED: 'UNITED ARAB EMIRATES',
            ARE: 'UNITED ARAB EMIRATES'
        }

        return countries[currencyCode]
    }

    return {
        isObjectEmpty,
        isEmptyArray,
        countArrayItems,
        generateRandomNumber,
        truncate,
        randomNumber,
        formatNumberwithComma,
        handleFileChange,
        isDateBeforeToday,
        currentDate,
        useReusableNavigation,
        capitalizeFirstLetter,
        convertToSlug,
        hasMixedCharacters,
        slug,
        getSlug,
        updateApiHeader,
        createApiHeader,
        patchApiHeader,
        humanReadableDate,
        generateUUID,
        getAssetPath,
        handleImageError,
        toastAlert,
        isWordInArray,
        createScript,
        checkPassword,
        maskEmail,
        formatNumber,
        unformatNumber,
        getInitials,
        acceptNumberOnly,
        acceptAmountField,
        stripExtraDots,
        formatDecimalNumber,
        isValidPhoneNumber,
        isValidSmartcardNumber,
        isValidMeterNumber,
        saveToLocalStore,
        getFromLocalStore,
        hideBalance,
        destroyToken,
        numberFormatter,
        stringWithoutSpaces,
        asianCountries,
        asianIpayCodes,
        allIpayCodes,
        mobileMoneyCountries,
        subtractYearsFromCurrentYear,
        sum,
        ipayRecipientCountryName
    }
}
