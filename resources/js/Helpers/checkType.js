// Using this function instead of the builtin typeof because
//      typeof [] is 'object' instead of 'array'
export const getType = (value) => {
    let type = toString.call(value).match(/\[object (\w+)\]/)[1];

    return {
        Array,
        Boolean,
        Date,
        Function,
        Number,
        Object,
        String,
    }[type];
};

export const isArray = (value) => getType(value) === Array;

export const isBoolean = (value) => getType(value) === Boolean;

export const isDate = (value) => getType(value) === Date;

export const isFunction = (value) => getType(value) === Function;

export const isNull = (value) => value === null;

export const isNumber = (value) => getType(value) === Number;

export const isObject = (value) => getType(value) === Object;

export const isString = (value) => getType(value) === String;

export const isUndefined = (value) => typeof value === 'undefined';
