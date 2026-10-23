var PortalRules = (() => {
  var __defProp = Object.defineProperty;
  var __getOwnPropDesc = Object.getOwnPropertyDescriptor;
  var __getOwnPropNames = Object.getOwnPropertyNames;
  var __hasOwnProp = Object.prototype.hasOwnProperty;
  var __export = (target, all) => {
    for (var name in all)
      __defProp(target, name, { get: all[name], enumerable: true });
  };
  var __copyProps = (to, from, except, desc) => {
    if (from && typeof from === "object" || typeof from === "function") {
      for (let key of __getOwnPropNames(from))
        if (!__hasOwnProp.call(to, key) && key !== except)
          __defProp(to, key, { get: () => from[key], enumerable: !(desc = __getOwnPropDesc(from, key)) || desc.enumerable });
    }
    return to;
  };
  var __toCommonJS = (mod) => __copyProps(__defProp({}, "__esModule", { value: true }), mod);

  // rules-entry.ts
  var rules_entry_exports = {};
  __export(rules_entry_exports, {
    SEED: () => SEED,
    csv: () => csv,
    emptyFilters: () => emptyFilters,
    filtered: () => filtered,
    listingSchema: () => listingSchema,
    matches: () => matches,
    proposalSchema: () => proposalSchema,
    remaining: () => remaining,
    skillGap: () => skillGap,
    summary: () => summary
  });

  // ../../../../sites/ket-noi-ba-nha-ninh-binh/node_modules/.pnpm/zod@3.25.76/node_modules/zod/v3/external.js
  var external_exports = {};
  __export(external_exports, {
    BRAND: () => BRAND,
    DIRTY: () => DIRTY,
    EMPTY_PATH: () => EMPTY_PATH,
    INVALID: () => INVALID,
    NEVER: () => NEVER,
    OK: () => OK,
    ParseStatus: () => ParseStatus,
    Schema: () => ZodType,
    ZodAny: () => ZodAny,
    ZodArray: () => ZodArray,
    ZodBigInt: () => ZodBigInt,
    ZodBoolean: () => ZodBoolean,
    ZodBranded: () => ZodBranded,
    ZodCatch: () => ZodCatch,
    ZodDate: () => ZodDate,
    ZodDefault: () => ZodDefault,
    ZodDiscriminatedUnion: () => ZodDiscriminatedUnion,
    ZodEffects: () => ZodEffects,
    ZodEnum: () => ZodEnum,
    ZodError: () => ZodError,
    ZodFirstPartyTypeKind: () => ZodFirstPartyTypeKind,
    ZodFunction: () => ZodFunction,
    ZodIntersection: () => ZodIntersection,
    ZodIssueCode: () => ZodIssueCode,
    ZodLazy: () => ZodLazy,
    ZodLiteral: () => ZodLiteral,
    ZodMap: () => ZodMap,
    ZodNaN: () => ZodNaN,
    ZodNativeEnum: () => ZodNativeEnum,
    ZodNever: () => ZodNever,
    ZodNull: () => ZodNull,
    ZodNullable: () => ZodNullable,
    ZodNumber: () => ZodNumber,
    ZodObject: () => ZodObject,
    ZodOptional: () => ZodOptional,
    ZodParsedType: () => ZodParsedType,
    ZodPipeline: () => ZodPipeline,
    ZodPromise: () => ZodPromise,
    ZodReadonly: () => ZodReadonly,
    ZodRecord: () => ZodRecord,
    ZodSchema: () => ZodType,
    ZodSet: () => ZodSet,
    ZodString: () => ZodString,
    ZodSymbol: () => ZodSymbol,
    ZodTransformer: () => ZodEffects,
    ZodTuple: () => ZodTuple,
    ZodType: () => ZodType,
    ZodUndefined: () => ZodUndefined,
    ZodUnion: () => ZodUnion,
    ZodUnknown: () => ZodUnknown,
    ZodVoid: () => ZodVoid,
    addIssueToContext: () => addIssueToContext,
    any: () => anyType,
    array: () => arrayType,
    bigint: () => bigIntType,
    boolean: () => booleanType,
    coerce: () => coerce,
    custom: () => custom,
    date: () => dateType,
    datetimeRegex: () => datetimeRegex,
    defaultErrorMap: () => en_default,
    discriminatedUnion: () => discriminatedUnionType,
    effect: () => effectsType,
    enum: () => enumType,
    function: () => functionType,
    getErrorMap: () => getErrorMap,
    getParsedType: () => getParsedType,
    instanceof: () => instanceOfType,
    intersection: () => intersectionType,
    isAborted: () => isAborted,
    isAsync: () => isAsync,
    isDirty: () => isDirty,
    isValid: () => isValid,
    late: () => late,
    lazy: () => lazyType,
    literal: () => literalType,
    makeIssue: () => makeIssue,
    map: () => mapType,
    nan: () => nanType,
    nativeEnum: () => nativeEnumType,
    never: () => neverType,
    null: () => nullType,
    nullable: () => nullableType,
    number: () => numberType,
    object: () => objectType,
    objectUtil: () => objectUtil,
    oboolean: () => oboolean,
    onumber: () => onumber,
    optional: () => optionalType,
    ostring: () => ostring,
    pipeline: () => pipelineType,
    preprocess: () => preprocessType,
    promise: () => promiseType,
    quotelessJson: () => quotelessJson,
    record: () => recordType,
    set: () => setType,
    setErrorMap: () => setErrorMap,
    strictObject: () => strictObjectType,
    string: () => stringType,
    symbol: () => symbolType,
    transformer: () => effectsType,
    tuple: () => tupleType,
    undefined: () => undefinedType,
    union: () => unionType,
    unknown: () => unknownType,
    util: () => util,
    void: () => voidType
  });

  // ../../../../sites/ket-noi-ba-nha-ninh-binh/node_modules/.pnpm/zod@3.25.76/node_modules/zod/v3/helpers/util.js
  var util;
  (function(util2) {
    util2.assertEqual = (_) => {
    };
    function assertIs(_arg) {
    }
    util2.assertIs = assertIs;
    function assertNever(_x) {
      throw new Error();
    }
    util2.assertNever = assertNever;
    util2.arrayToEnum = (items) => {
      const obj = {};
      for (const item of items) {
        obj[item] = item;
      }
      return obj;
    };
    util2.getValidEnumValues = (obj) => {
      const validKeys = util2.objectKeys(obj).filter((k) => typeof obj[obj[k]] !== "number");
      const filtered2 = {};
      for (const k of validKeys) {
        filtered2[k] = obj[k];
      }
      return util2.objectValues(filtered2);
    };
    util2.objectValues = (obj) => {
      return util2.objectKeys(obj).map(function(e) {
        return obj[e];
      });
    };
    util2.objectKeys = typeof Object.keys === "function" ? (obj) => Object.keys(obj) : (object) => {
      const keys = [];
      for (const key in object) {
        if (Object.prototype.hasOwnProperty.call(object, key)) {
          keys.push(key);
        }
      }
      return keys;
    };
    util2.find = (arr, checker) => {
      for (const item of arr) {
        if (checker(item))
          return item;
      }
      return void 0;
    };
    util2.isInteger = typeof Number.isInteger === "function" ? (val) => Number.isInteger(val) : (val) => typeof val === "number" && Number.isFinite(val) && Math.floor(val) === val;
    function joinValues(array, separator = " | ") {
      return array.map((val) => typeof val === "string" ? `'${val}'` : val).join(separator);
    }
    util2.joinValues = joinValues;
    util2.jsonStringifyReplacer = (_, value) => {
      if (typeof value === "bigint") {
        return value.toString();
      }
      return value;
    };
  })(util || (util = {}));
  var objectUtil;
  (function(objectUtil2) {
    objectUtil2.mergeShapes = (first, second) => {
      return {
        ...first,
        ...second
        // second overwrites first
      };
    };
  })(objectUtil || (objectUtil = {}));
  var ZodParsedType = util.arrayToEnum([
    "string",
    "nan",
    "number",
    "integer",
    "float",
    "boolean",
    "date",
    "bigint",
    "symbol",
    "function",
    "undefined",
    "null",
    "array",
    "object",
    "unknown",
    "promise",
    "void",
    "never",
    "map",
    "set"
  ]);
  var getParsedType = (data) => {
    const t = typeof data;
    switch (t) {
      case "undefined":
        return ZodParsedType.undefined;
      case "string":
        return ZodParsedType.string;
      case "number":
        return Number.isNaN(data) ? ZodParsedType.nan : ZodParsedType.number;
      case "boolean":
        return ZodParsedType.boolean;
      case "function":
        return ZodParsedType.function;
      case "bigint":
        return ZodParsedType.bigint;
      case "symbol":
        return ZodParsedType.symbol;
      case "object":
        if (Array.isArray(data)) {
          return ZodParsedType.array;
        }
        if (data === null) {
          return ZodParsedType.null;
        }
        if (data.then && typeof data.then === "function" && data.catch && typeof data.catch === "function") {
          return ZodParsedType.promise;
        }
        if (typeof Map !== "undefined" && data instanceof Map) {
          return ZodParsedType.map;
        }
        if (typeof Set !== "undefined" && data instanceof Set) {
          return ZodParsedType.set;
        }
        if (typeof Date !== "undefined" && data instanceof Date) {
          return ZodParsedType.date;
        }
        return ZodParsedType.object;
      default:
        return ZodParsedType.unknown;
    }
  };

  // ../../../../sites/ket-noi-ba-nha-ninh-binh/node_modules/.pnpm/zod@3.25.76/node_modules/zod/v3/ZodError.js
  var ZodIssueCode = util.arrayToEnum([
    "invalid_type",
    "invalid_literal",
    "custom",
    "invalid_union",
    "invalid_union_discriminator",
    "invalid_enum_value",
    "unrecognized_keys",
    "invalid_arguments",
    "invalid_return_type",
    "invalid_date",
    "invalid_string",
    "too_small",
    "too_big",
    "invalid_intersection_types",
    "not_multiple_of",
    "not_finite"
  ]);
  var quotelessJson = (obj) => {
    const json = JSON.stringify(obj, null, 2);
    return json.replace(/"([^"]+)":/g, "$1:");
  };
  var ZodError = class _ZodError extends Error {
    get errors() {
      return this.issues;
    }
    constructor(issues) {
      super();
      this.issues = [];
      this.addIssue = (sub) => {
        this.issues = [...this.issues, sub];
      };
      this.addIssues = (subs = []) => {
        this.issues = [...this.issues, ...subs];
      };
      const actualProto = new.target.prototype;
      if (Object.setPrototypeOf) {
        Object.setPrototypeOf(this, actualProto);
      } else {
        this.__proto__ = actualProto;
      }
      this.name = "ZodError";
      this.issues = issues;
    }
    format(_mapper) {
      const mapper = _mapper || function(issue) {
        return issue.message;
      };
      const fieldErrors = { _errors: [] };
      const processError = (error) => {
        for (const issue of error.issues) {
          if (issue.code === "invalid_union") {
            issue.unionErrors.map(processError);
          } else if (issue.code === "invalid_return_type") {
            processError(issue.returnTypeError);
          } else if (issue.code === "invalid_arguments") {
            processError(issue.argumentsError);
          } else if (issue.path.length === 0) {
            fieldErrors._errors.push(mapper(issue));
          } else {
            let curr = fieldErrors;
            let i = 0;
            while (i < issue.path.length) {
              const el = issue.path[i];
              const terminal = i === issue.path.length - 1;
              if (!terminal) {
                curr[el] = curr[el] || { _errors: [] };
              } else {
                curr[el] = curr[el] || { _errors: [] };
                curr[el]._errors.push(mapper(issue));
              }
              curr = curr[el];
              i++;
            }
          }
        }
      };
      processError(this);
      return fieldErrors;
    }
    static assert(value) {
      if (!(value instanceof _ZodError)) {
        throw new Error(`Not a ZodError: ${value}`);
      }
    }
    toString() {
      return this.message;
    }
    get message() {
      return JSON.stringify(this.issues, util.jsonStringifyReplacer, 2);
    }
    get isEmpty() {
      return this.issues.length === 0;
    }
    flatten(mapper = (issue) => issue.message) {
      const fieldErrors = {};
      const formErrors = [];
      for (const sub of this.issues) {
        if (sub.path.length > 0) {
          const firstEl = sub.path[0];
          fieldErrors[firstEl] = fieldErrors[firstEl] || [];
          fieldErrors[firstEl].push(mapper(sub));
        } else {
          formErrors.push(mapper(sub));
        }
      }
      return { formErrors, fieldErrors };
    }
    get formErrors() {
      return this.flatten();
    }
  };
  ZodError.create = (issues) => {
    const error = new ZodError(issues);
    return error;
  };

  // ../../../../sites/ket-noi-ba-nha-ninh-binh/node_modules/.pnpm/zod@3.25.76/node_modules/zod/v3/locales/en.js
  var errorMap = (issue, _ctx) => {
    let message;
    switch (issue.code) {
      case ZodIssueCode.invalid_type:
        if (issue.received === ZodParsedType.undefined) {
          message = "Required";
        } else {
          message = `Expected ${issue.expected}, received ${issue.received}`;
        }
        break;
      case ZodIssueCode.invalid_literal:
        message = `Invalid literal value, expected ${JSON.stringify(issue.expected, util.jsonStringifyReplacer)}`;
        break;
      case ZodIssueCode.unrecognized_keys:
        message = `Unrecognized key(s) in object: ${util.joinValues(issue.keys, ", ")}`;
        break;
      case ZodIssueCode.invalid_union:
        message = `Invalid input`;
        break;
      case ZodIssueCode.invalid_union_discriminator:
        message = `Invalid discriminator value. Expected ${util.joinValues(issue.options)}`;
        break;
      case ZodIssueCode.invalid_enum_value:
        message = `Invalid enum value. Expected ${util.joinValues(issue.options)}, received '${issue.received}'`;
        break;
      case ZodIssueCode.invalid_arguments:
        message = `Invalid function arguments`;
        break;
      case ZodIssueCode.invalid_return_type:
        message = `Invalid function return type`;
        break;
      case ZodIssueCode.invalid_date:
        message = `Invalid date`;
        break;
      case ZodIssueCode.invalid_string:
        if (typeof issue.validation === "object") {
          if ("includes" in issue.validation) {
            message = `Invalid input: must include "${issue.validation.includes}"`;
            if (typeof issue.validation.position === "number") {
              message = `${message} at one or more positions greater than or equal to ${issue.validation.position}`;
            }
          } else if ("startsWith" in issue.validation) {
            message = `Invalid input: must start with "${issue.validation.startsWith}"`;
          } else if ("endsWith" in issue.validation) {
            message = `Invalid input: must end with "${issue.validation.endsWith}"`;
          } else {
            util.assertNever(issue.validation);
          }
        } else if (issue.validation !== "regex") {
          message = `Invalid ${issue.validation}`;
        } else {
          message = "Invalid";
        }
        break;
      case ZodIssueCode.too_small:
        if (issue.type === "array")
          message = `Array must contain ${issue.exact ? "exactly" : issue.inclusive ? `at least` : `more than`} ${issue.minimum} element(s)`;
        else if (issue.type === "string")
          message = `String must contain ${issue.exact ? "exactly" : issue.inclusive ? `at least` : `over`} ${issue.minimum} character(s)`;
        else if (issue.type === "number")
          message = `Number must be ${issue.exact ? `exactly equal to ` : issue.inclusive ? `greater than or equal to ` : `greater than `}${issue.minimum}`;
        else if (issue.type === "bigint")
          message = `Number must be ${issue.exact ? `exactly equal to ` : issue.inclusive ? `greater than or equal to ` : `greater than `}${issue.minimum}`;
        else if (issue.type === "date")
          message = `Date must be ${issue.exact ? `exactly equal to ` : issue.inclusive ? `greater than or equal to ` : `greater than `}${new Date(Number(issue.minimum))}`;
        else
          message = "Invalid input";
        break;
      case ZodIssueCode.too_big:
        if (issue.type === "array")
          message = `Array must contain ${issue.exact ? `exactly` : issue.inclusive ? `at most` : `less than`} ${issue.maximum} element(s)`;
        else if (issue.type === "string")
          message = `String must contain ${issue.exact ? `exactly` : issue.inclusive ? `at most` : `under`} ${issue.maximum} character(s)`;
        else if (issue.type === "number")
          message = `Number must be ${issue.exact ? `exactly` : issue.inclusive ? `less than or equal to` : `less than`} ${issue.maximum}`;
        else if (issue.type === "bigint")
          message = `BigInt must be ${issue.exact ? `exactly` : issue.inclusive ? `less than or equal to` : `less than`} ${issue.maximum}`;
        else if (issue.type === "date")
          message = `Date must be ${issue.exact ? `exactly` : issue.inclusive ? `smaller than or equal to` : `smaller than`} ${new Date(Number(issue.maximum))}`;
        else
          message = "Invalid input";
        break;
      case ZodIssueCode.custom:
        message = `Invalid input`;
        break;
      case ZodIssueCode.invalid_intersection_types:
        message = `Intersection results could not be merged`;
        break;
      case ZodIssueCode.not_multiple_of:
        message = `Number must be a multiple of ${issue.multipleOf}`;
        break;
      case ZodIssueCode.not_finite:
        message = "Number must be finite";
        break;
      default:
        message = _ctx.defaultError;
        util.assertNever(issue);
    }
    return { message };
  };
  var en_default = errorMap;

  // ../../../../sites/ket-noi-ba-nha-ninh-binh/node_modules/.pnpm/zod@3.25.76/node_modules/zod/v3/errors.js
  var overrideErrorMap = en_default;
  function setErrorMap(map) {
    overrideErrorMap = map;
  }
  function getErrorMap() {
    return overrideErrorMap;
  }

  // ../../../../sites/ket-noi-ba-nha-ninh-binh/node_modules/.pnpm/zod@3.25.76/node_modules/zod/v3/helpers/parseUtil.js
  var makeIssue = (params) => {
    const { data, path, errorMaps, issueData } = params;
    const fullPath = [...path, ...issueData.path || []];
    const fullIssue = {
      ...issueData,
      path: fullPath
    };
    if (issueData.message !== void 0) {
      return {
        ...issueData,
        path: fullPath,
        message: issueData.message
      };
    }
    let errorMessage = "";
    const maps = errorMaps.filter((m) => !!m).slice().reverse();
    for (const map of maps) {
      errorMessage = map(fullIssue, { data, defaultError: errorMessage }).message;
    }
    return {
      ...issueData,
      path: fullPath,
      message: errorMessage
    };
  };
  var EMPTY_PATH = [];
  function addIssueToContext(ctx, issueData) {
    const overrideMap = getErrorMap();
    const issue = makeIssue({
      issueData,
      data: ctx.data,
      path: ctx.path,
      errorMaps: [
        ctx.common.contextualErrorMap,
        // contextual error map is first priority
        ctx.schemaErrorMap,
        // then schema-bound map if available
        overrideMap,
        // then global override map
        overrideMap === en_default ? void 0 : en_default
        // then global default map
      ].filter((x) => !!x)
    });
    ctx.common.issues.push(issue);
  }
  var ParseStatus = class _ParseStatus {
    constructor() {
      this.value = "valid";
    }
    dirty() {
      if (this.value === "valid")
        this.value = "dirty";
    }
    abort() {
      if (this.value !== "aborted")
        this.value = "aborted";
    }
    static mergeArray(status, results) {
      const arrayValue = [];
      for (const s of results) {
        if (s.status === "aborted")
          return INVALID;
        if (s.status === "dirty")
          status.dirty();
        arrayValue.push(s.value);
      }
      return { status: status.value, value: arrayValue };
    }
    static async mergeObjectAsync(status, pairs) {
      const syncPairs = [];
      for (const pair of pairs) {
        const key = await pair.key;
        const value = await pair.value;
        syncPairs.push({
          key,
          value
        });
      }
      return _ParseStatus.mergeObjectSync(status, syncPairs);
    }
    static mergeObjectSync(status, pairs) {
      const finalObject = {};
      for (const pair of pairs) {
        const { key, value } = pair;
        if (key.status === "aborted")
          return INVALID;
        if (value.status === "aborted")
          return INVALID;
        if (key.status === "dirty")
          status.dirty();
        if (value.status === "dirty")
          status.dirty();
        if (key.value !== "__proto__" && (typeof value.value !== "undefined" || pair.alwaysSet)) {
          finalObject[key.value] = value.value;
        }
      }
      return { status: status.value, value: finalObject };
    }
  };
  var INVALID = Object.freeze({
    status: "aborted"
  });
  var DIRTY = (value) => ({ status: "dirty", value });
  var OK = (value) => ({ status: "valid", value });
  var isAborted = (x) => x.status === "aborted";
  var isDirty = (x) => x.status === "dirty";
  var isValid = (x) => x.status === "valid";
  var isAsync = (x) => typeof Promise !== "undefined" && x instanceof Promise;

  // ../../../../sites/ket-noi-ba-nha-ninh-binh/node_modules/.pnpm/zod@3.25.76/node_modules/zod/v3/helpers/errorUtil.js
  var errorUtil;
  (function(errorUtil2) {
    errorUtil2.errToObj = (message) => typeof message === "string" ? { message } : message || {};
    errorUtil2.toString = (message) => typeof message === "string" ? message : message?.message;
  })(errorUtil || (errorUtil = {}));

  // ../../../../sites/ket-noi-ba-nha-ninh-binh/node_modules/.pnpm/zod@3.25.76/node_modules/zod/v3/types.js
  var ParseInputLazyPath = class {
    constructor(parent, value, path, key) {
      this._cachedPath = [];
      this.parent = parent;
      this.data = value;
      this._path = path;
      this._key = key;
    }
    get path() {
      if (!this._cachedPath.length) {
        if (Array.isArray(this._key)) {
          this._cachedPath.push(...this._path, ...this._key);
        } else {
          this._cachedPath.push(...this._path, this._key);
        }
      }
      return this._cachedPath;
    }
  };
  var handleResult = (ctx, result) => {
    if (isValid(result)) {
      return { success: true, data: result.value };
    } else {
      if (!ctx.common.issues.length) {
        throw new Error("Validation failed but no issues detected.");
      }
      return {
        success: false,
        get error() {
          if (this._error)
            return this._error;
          const error = new ZodError(ctx.common.issues);
          this._error = error;
          return this._error;
        }
      };
    }
  };
  function processCreateParams(params) {
    if (!params)
      return {};
    const { errorMap: errorMap2, invalid_type_error, required_error, description } = params;
    if (errorMap2 && (invalid_type_error || required_error)) {
      throw new Error(`Can't use "invalid_type_error" or "required_error" in conjunction with custom error map.`);
    }
    if (errorMap2)
      return { errorMap: errorMap2, description };
    const customMap = (iss, ctx) => {
      const { message } = params;
      if (iss.code === "invalid_enum_value") {
        return { message: message ?? ctx.defaultError };
      }
      if (typeof ctx.data === "undefined") {
        return { message: message ?? required_error ?? ctx.defaultError };
      }
      if (iss.code !== "invalid_type")
        return { message: ctx.defaultError };
      return { message: message ?? invalid_type_error ?? ctx.defaultError };
    };
    return { errorMap: customMap, description };
  }
  var ZodType = class {
    get description() {
      return this._def.description;
    }
    _getType(input) {
      return getParsedType(input.data);
    }
    _getOrReturnCtx(input, ctx) {
      return ctx || {
        common: input.parent.common,
        data: input.data,
        parsedType: getParsedType(input.data),
        schemaErrorMap: this._def.errorMap,
        path: input.path,
        parent: input.parent
      };
    }
    _processInputParams(input) {
      return {
        status: new ParseStatus(),
        ctx: {
          common: input.parent.common,
          data: input.data,
          parsedType: getParsedType(input.data),
          schemaErrorMap: this._def.errorMap,
          path: input.path,
          parent: input.parent
        }
      };
    }
    _parseSync(input) {
      const result = this._parse(input);
      if (isAsync(result)) {
        throw new Error("Synchronous parse encountered promise.");
      }
      return result;
    }
    _parseAsync(input) {
      const result = this._parse(input);
      return Promise.resolve(result);
    }
    parse(data, params) {
      const result = this.safeParse(data, params);
      if (result.success)
        return result.data;
      throw result.error;
    }
    safeParse(data, params) {
      const ctx = {
        common: {
          issues: [],
          async: params?.async ?? false,
          contextualErrorMap: params?.errorMap
        },
        path: params?.path || [],
        schemaErrorMap: this._def.errorMap,
        parent: null,
        data,
        parsedType: getParsedType(data)
      };
      const result = this._parseSync({ data, path: ctx.path, parent: ctx });
      return handleResult(ctx, result);
    }
    "~validate"(data) {
      const ctx = {
        common: {
          issues: [],
          async: !!this["~standard"].async
        },
        path: [],
        schemaErrorMap: this._def.errorMap,
        parent: null,
        data,
        parsedType: getParsedType(data)
      };
      if (!this["~standard"].async) {
        try {
          const result = this._parseSync({ data, path: [], parent: ctx });
          return isValid(result) ? {
            value: result.value
          } : {
            issues: ctx.common.issues
          };
        } catch (err) {
          if (err?.message?.toLowerCase()?.includes("encountered")) {
            this["~standard"].async = true;
          }
          ctx.common = {
            issues: [],
            async: true
          };
        }
      }
      return this._parseAsync({ data, path: [], parent: ctx }).then((result) => isValid(result) ? {
        value: result.value
      } : {
        issues: ctx.common.issues
      });
    }
    async parseAsync(data, params) {
      const result = await this.safeParseAsync(data, params);
      if (result.success)
        return result.data;
      throw result.error;
    }
    async safeParseAsync(data, params) {
      const ctx = {
        common: {
          issues: [],
          contextualErrorMap: params?.errorMap,
          async: true
        },
        path: params?.path || [],
        schemaErrorMap: this._def.errorMap,
        parent: null,
        data,
        parsedType: getParsedType(data)
      };
      const maybeAsyncResult = this._parse({ data, path: ctx.path, parent: ctx });
      const result = await (isAsync(maybeAsyncResult) ? maybeAsyncResult : Promise.resolve(maybeAsyncResult));
      return handleResult(ctx, result);
    }
    refine(check, message) {
      const getIssueProperties = (val) => {
        if (typeof message === "string" || typeof message === "undefined") {
          return { message };
        } else if (typeof message === "function") {
          return message(val);
        } else {
          return message;
        }
      };
      return this._refinement((val, ctx) => {
        const result = check(val);
        const setError = () => ctx.addIssue({
          code: ZodIssueCode.custom,
          ...getIssueProperties(val)
        });
        if (typeof Promise !== "undefined" && result instanceof Promise) {
          return result.then((data) => {
            if (!data) {
              setError();
              return false;
            } else {
              return true;
            }
          });
        }
        if (!result) {
          setError();
          return false;
        } else {
          return true;
        }
      });
    }
    refinement(check, refinementData) {
      return this._refinement((val, ctx) => {
        if (!check(val)) {
          ctx.addIssue(typeof refinementData === "function" ? refinementData(val, ctx) : refinementData);
          return false;
        } else {
          return true;
        }
      });
    }
    _refinement(refinement) {
      return new ZodEffects({
        schema: this,
        typeName: ZodFirstPartyTypeKind.ZodEffects,
        effect: { type: "refinement", refinement }
      });
    }
    superRefine(refinement) {
      return this._refinement(refinement);
    }
    constructor(def) {
      this.spa = this.safeParseAsync;
      this._def = def;
      this.parse = this.parse.bind(this);
      this.safeParse = this.safeParse.bind(this);
      this.parseAsync = this.parseAsync.bind(this);
      this.safeParseAsync = this.safeParseAsync.bind(this);
      this.spa = this.spa.bind(this);
      this.refine = this.refine.bind(this);
      this.refinement = this.refinement.bind(this);
      this.superRefine = this.superRefine.bind(this);
      this.optional = this.optional.bind(this);
      this.nullable = this.nullable.bind(this);
      this.nullish = this.nullish.bind(this);
      this.array = this.array.bind(this);
      this.promise = this.promise.bind(this);
      this.or = this.or.bind(this);
      this.and = this.and.bind(this);
      this.transform = this.transform.bind(this);
      this.brand = this.brand.bind(this);
      this.default = this.default.bind(this);
      this.catch = this.catch.bind(this);
      this.describe = this.describe.bind(this);
      this.pipe = this.pipe.bind(this);
      this.readonly = this.readonly.bind(this);
      this.isNullable = this.isNullable.bind(this);
      this.isOptional = this.isOptional.bind(this);
      this["~standard"] = {
        version: 1,
        vendor: "zod",
        validate: (data) => this["~validate"](data)
      };
    }
    optional() {
      return ZodOptional.create(this, this._def);
    }
    nullable() {
      return ZodNullable.create(this, this._def);
    }
    nullish() {
      return this.nullable().optional();
    }
    array() {
      return ZodArray.create(this);
    }
    promise() {
      return ZodPromise.create(this, this._def);
    }
    or(option) {
      return ZodUnion.create([this, option], this._def);
    }
    and(incoming) {
      return ZodIntersection.create(this, incoming, this._def);
    }
    transform(transform) {
      return new ZodEffects({
        ...processCreateParams(this._def),
        schema: this,
        typeName: ZodFirstPartyTypeKind.ZodEffects,
        effect: { type: "transform", transform }
      });
    }
    default(def) {
      const defaultValueFunc = typeof def === "function" ? def : () => def;
      return new ZodDefault({
        ...processCreateParams(this._def),
        innerType: this,
        defaultValue: defaultValueFunc,
        typeName: ZodFirstPartyTypeKind.ZodDefault
      });
    }
    brand() {
      return new ZodBranded({
        typeName: ZodFirstPartyTypeKind.ZodBranded,
        type: this,
        ...processCreateParams(this._def)
      });
    }
    catch(def) {
      const catchValueFunc = typeof def === "function" ? def : () => def;
      return new ZodCatch({
        ...processCreateParams(this._def),
        innerType: this,
        catchValue: catchValueFunc,
        typeName: ZodFirstPartyTypeKind.ZodCatch
      });
    }
    describe(description) {
      const This = this.constructor;
      return new This({
        ...this._def,
        description
      });
    }
    pipe(target) {
      return ZodPipeline.create(this, target);
    }
    readonly() {
      return ZodReadonly.create(this);
    }
    isOptional() {
      return this.safeParse(void 0).success;
    }
    isNullable() {
      return this.safeParse(null).success;
    }
  };
  var cuidRegex = /^c[^\s-]{8,}$/i;
  var cuid2Regex = /^[0-9a-z]+$/;
  var ulidRegex = /^[0-9A-HJKMNP-TV-Z]{26}$/i;
  var uuidRegex = /^[0-9a-fA-F]{8}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{12}$/i;
  var nanoidRegex = /^[a-z0-9_-]{21}$/i;
  var jwtRegex = /^[A-Za-z0-9-_]+\.[A-Za-z0-9-_]+\.[A-Za-z0-9-_]*$/;
  var durationRegex = /^[-+]?P(?!$)(?:(?:[-+]?\d+Y)|(?:[-+]?\d+[.,]\d+Y$))?(?:(?:[-+]?\d+M)|(?:[-+]?\d+[.,]\d+M$))?(?:(?:[-+]?\d+W)|(?:[-+]?\d+[.,]\d+W$))?(?:(?:[-+]?\d+D)|(?:[-+]?\d+[.,]\d+D$))?(?:T(?=[\d+-])(?:(?:[-+]?\d+H)|(?:[-+]?\d+[.,]\d+H$))?(?:(?:[-+]?\d+M)|(?:[-+]?\d+[.,]\d+M$))?(?:[-+]?\d+(?:[.,]\d+)?S)?)??$/;
  var emailRegex = /^(?!\.)(?!.*\.\.)([A-Z0-9_'+\-\.]*)[A-Z0-9_+-]@([A-Z0-9][A-Z0-9\-]*\.)+[A-Z]{2,}$/i;
  var _emojiRegex = `^(\\p{Extended_Pictographic}|\\p{Emoji_Component})+$`;
  var emojiRegex;
  var ipv4Regex = /^(?:(?:25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9][0-9]|[0-9])\.){3}(?:25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9][0-9]|[0-9])$/;
  var ipv4CidrRegex = /^(?:(?:25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9][0-9]|[0-9])\.){3}(?:25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9][0-9]|[0-9])\/(3[0-2]|[12]?[0-9])$/;
  var ipv6Regex = /^(([0-9a-fA-F]{1,4}:){7,7}[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,7}:|([0-9a-fA-F]{1,4}:){1,6}:[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,5}(:[0-9a-fA-F]{1,4}){1,2}|([0-9a-fA-F]{1,4}:){1,4}(:[0-9a-fA-F]{1,4}){1,3}|([0-9a-fA-F]{1,4}:){1,3}(:[0-9a-fA-F]{1,4}){1,4}|([0-9a-fA-F]{1,4}:){1,2}(:[0-9a-fA-F]{1,4}){1,5}|[0-9a-fA-F]{1,4}:((:[0-9a-fA-F]{1,4}){1,6})|:((:[0-9a-fA-F]{1,4}){1,7}|:)|fe80:(:[0-9a-fA-F]{0,4}){0,4}%[0-9a-zA-Z]{1,}|::(ffff(:0{1,4}){0,1}:){0,1}((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\.){3,3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])|([0-9a-fA-F]{1,4}:){1,4}:((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\.){3,3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9]))$/;
  var ipv6CidrRegex = /^(([0-9a-fA-F]{1,4}:){7,7}[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,7}:|([0-9a-fA-F]{1,4}:){1,6}:[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,5}(:[0-9a-fA-F]{1,4}){1,2}|([0-9a-fA-F]{1,4}:){1,4}(:[0-9a-fA-F]{1,4}){1,3}|([0-9a-fA-F]{1,4}:){1,3}(:[0-9a-fA-F]{1,4}){1,4}|([0-9a-fA-F]{1,4}:){1,2}(:[0-9a-fA-F]{1,4}){1,5}|[0-9a-fA-F]{1,4}:((:[0-9a-fA-F]{1,4}){1,6})|:((:[0-9a-fA-F]{1,4}){1,7}|:)|fe80:(:[0-9a-fA-F]{0,4}){0,4}%[0-9a-zA-Z]{1,}|::(ffff(:0{1,4}){0,1}:){0,1}((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\.){3,3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])|([0-9a-fA-F]{1,4}:){1,4}:((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\.){3,3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9]))\/(12[0-8]|1[01][0-9]|[1-9]?[0-9])$/;
  var base64Regex = /^([0-9a-zA-Z+/]{4})*(([0-9a-zA-Z+/]{2}==)|([0-9a-zA-Z+/]{3}=))?$/;
  var base64urlRegex = /^([0-9a-zA-Z-_]{4})*(([0-9a-zA-Z-_]{2}(==)?)|([0-9a-zA-Z-_]{3}(=)?))?$/;
  var dateRegexSource = `((\\d\\d[2468][048]|\\d\\d[13579][26]|\\d\\d0[48]|[02468][048]00|[13579][26]00)-02-29|\\d{4}-((0[13578]|1[02])-(0[1-9]|[12]\\d|3[01])|(0[469]|11)-(0[1-9]|[12]\\d|30)|(02)-(0[1-9]|1\\d|2[0-8])))`;
  var dateRegex = new RegExp(`^${dateRegexSource}$`);
  function timeRegexSource(args) {
    let secondsRegexSource = `[0-5]\\d`;
    if (args.precision) {
      secondsRegexSource = `${secondsRegexSource}\\.\\d{${args.precision}}`;
    } else if (args.precision == null) {
      secondsRegexSource = `${secondsRegexSource}(\\.\\d+)?`;
    }
    const secondsQuantifier = args.precision ? "+" : "?";
    return `([01]\\d|2[0-3]):[0-5]\\d(:${secondsRegexSource})${secondsQuantifier}`;
  }
  function timeRegex(args) {
    return new RegExp(`^${timeRegexSource(args)}$`);
  }
  function datetimeRegex(args) {
    let regex = `${dateRegexSource}T${timeRegexSource(args)}`;
    const opts = [];
    opts.push(args.local ? `Z?` : `Z`);
    if (args.offset)
      opts.push(`([+-]\\d{2}:?\\d{2})`);
    regex = `${regex}(${opts.join("|")})`;
    return new RegExp(`^${regex}$`);
  }
  function isValidIP(ip, version) {
    if ((version === "v4" || !version) && ipv4Regex.test(ip)) {
      return true;
    }
    if ((version === "v6" || !version) && ipv6Regex.test(ip)) {
      return true;
    }
    return false;
  }
  function isValidJWT(jwt, alg) {
    if (!jwtRegex.test(jwt))
      return false;
    try {
      const [header] = jwt.split(".");
      if (!header)
        return false;
      const base64 = header.replace(/-/g, "+").replace(/_/g, "/").padEnd(header.length + (4 - header.length % 4) % 4, "=");
      const decoded = JSON.parse(atob(base64));
      if (typeof decoded !== "object" || decoded === null)
        return false;
      if ("typ" in decoded && decoded?.typ !== "JWT")
        return false;
      if (!decoded.alg)
        return false;
      if (alg && decoded.alg !== alg)
        return false;
      return true;
    } catch {
      return false;
    }
  }
  function isValidCidr(ip, version) {
    if ((version === "v4" || !version) && ipv4CidrRegex.test(ip)) {
      return true;
    }
    if ((version === "v6" || !version) && ipv6CidrRegex.test(ip)) {
      return true;
    }
    return false;
  }
  var ZodString = class _ZodString extends ZodType {
    _parse(input) {
      if (this._def.coerce) {
        input.data = String(input.data);
      }
      const parsedType = this._getType(input);
      if (parsedType !== ZodParsedType.string) {
        const ctx2 = this._getOrReturnCtx(input);
        addIssueToContext(ctx2, {
          code: ZodIssueCode.invalid_type,
          expected: ZodParsedType.string,
          received: ctx2.parsedType
        });
        return INVALID;
      }
      const status = new ParseStatus();
      let ctx = void 0;
      for (const check of this._def.checks) {
        if (check.kind === "min") {
          if (input.data.length < check.value) {
            ctx = this._getOrReturnCtx(input, ctx);
            addIssueToContext(ctx, {
              code: ZodIssueCode.too_small,
              minimum: check.value,
              type: "string",
              inclusive: true,
              exact: false,
              message: check.message
            });
            status.dirty();
          }
        } else if (check.kind === "max") {
          if (input.data.length > check.value) {
            ctx = this._getOrReturnCtx(input, ctx);
            addIssueToContext(ctx, {
              code: ZodIssueCode.too_big,
              maximum: check.value,
              type: "string",
              inclusive: true,
              exact: false,
              message: check.message
            });
            status.dirty();
          }
        } else if (check.kind === "length") {
          const tooBig = input.data.length > check.value;
          const tooSmall = input.data.length < check.value;
          if (tooBig || tooSmall) {
            ctx = this._getOrReturnCtx(input, ctx);
            if (tooBig) {
              addIssueToContext(ctx, {
                code: ZodIssueCode.too_big,
                maximum: check.value,
                type: "string",
                inclusive: true,
                exact: true,
                message: check.message
              });
            } else if (tooSmall) {
              addIssueToContext(ctx, {
                code: ZodIssueCode.too_small,
                minimum: check.value,
                type: "string",
                inclusive: true,
                exact: true,
                message: check.message
              });
            }
            status.dirty();
          }
        } else if (check.kind === "email") {
          if (!emailRegex.test(input.data)) {
            ctx = this._getOrReturnCtx(input, ctx);
            addIssueToContext(ctx, {
              validation: "email",
              code: ZodIssueCode.invalid_string,
              message: check.message
            });
            status.dirty();
          }
        } else if (check.kind === "emoji") {
          if (!emojiRegex) {
            emojiRegex = new RegExp(_emojiRegex, "u");
          }
          if (!emojiRegex.test(input.data)) {
            ctx = this._getOrReturnCtx(input, ctx);
            addIssueToContext(ctx, {
              validation: "emoji",
              code: ZodIssueCode.invalid_string,
              message: check.message
            });
            status.dirty();
          }
        } else if (check.kind === "uuid") {
          if (!uuidRegex.test(input.data)) {
            ctx = this._getOrReturnCtx(input, ctx);
            addIssueToContext(ctx, {
              validation: "uuid",
              code: ZodIssueCode.invalid_string,
              message: check.message
            });
            status.dirty();
          }
        } else if (check.kind === "nanoid") {
          if (!nanoidRegex.test(input.data)) {
            ctx = this._getOrReturnCtx(input, ctx);
            addIssueToContext(ctx, {
              validation: "nanoid",
              code: ZodIssueCode.invalid_string,
              message: check.message
            });
            status.dirty();
          }
        } else if (check.kind === "cuid") {
          if (!cuidRegex.test(input.data)) {
            ctx = this._getOrReturnCtx(input, ctx);
            addIssueToContext(ctx, {
              validation: "cuid",
              code: ZodIssueCode.invalid_string,
              message: check.message
            });
            status.dirty();
          }
        } else if (check.kind === "cuid2") {
          if (!cuid2Regex.test(input.data)) {
            ctx = this._getOrReturnCtx(input, ctx);
            addIssueToContext(ctx, {
              validation: "cuid2",
              code: ZodIssueCode.invalid_string,
              message: check.message
            });
            status.dirty();
          }
        } else if (check.kind === "ulid") {
          if (!ulidRegex.test(input.data)) {
            ctx = this._getOrReturnCtx(input, ctx);
            addIssueToContext(ctx, {
              validation: "ulid",
              code: ZodIssueCode.invalid_string,
              message: check.message
            });
            status.dirty();
          }
        } else if (check.kind === "url") {
          try {
            new URL(input.data);
          } catch {
            ctx = this._getOrReturnCtx(input, ctx);
            addIssueToContext(ctx, {
              validation: "url",
              code: ZodIssueCode.invalid_string,
              message: check.message
            });
            status.dirty();
          }
        } else if (check.kind === "regex") {
          check.regex.lastIndex = 0;
          const testResult = check.regex.test(input.data);
          if (!testResult) {
            ctx = this._getOrReturnCtx(input, ctx);
            addIssueToContext(ctx, {
              validation: "regex",
              code: ZodIssueCode.invalid_string,
              message: check.message
            });
            status.dirty();
          }
        } else if (check.kind === "trim") {
          input.data = input.data.trim();
        } else if (check.kind === "includes") {
          if (!input.data.includes(check.value, check.position)) {
            ctx = this._getOrReturnCtx(input, ctx);
            addIssueToContext(ctx, {
              code: ZodIssueCode.invalid_string,
              validation: { includes: check.value, position: check.position },
              message: check.message
            });
            status.dirty();
          }
        } else if (check.kind === "toLowerCase") {
          input.data = input.data.toLowerCase();
        } else if (check.kind === "toUpperCase") {
          input.data = input.data.toUpperCase();
        } else if (check.kind === "startsWith") {
          if (!input.data.startsWith(check.value)) {
            ctx = this._getOrReturnCtx(input, ctx);
            addIssueToContext(ctx, {
              code: ZodIssueCode.invalid_string,
              validation: { startsWith: check.value },
              message: check.message
            });
            status.dirty();
          }
        } else if (check.kind === "endsWith") {
          if (!input.data.endsWith(check.value)) {
            ctx = this._getOrReturnCtx(input, ctx);
            addIssueToContext(ctx, {
              code: ZodIssueCode.invalid_string,
              validation: { endsWith: check.value },
              message: check.message
            });
            status.dirty();
          }
        } else if (check.kind === "datetime") {
          const regex = datetimeRegex(check);
          if (!regex.test(input.data)) {
            ctx = this._getOrReturnCtx(input, ctx);
            addIssueToContext(ctx, {
              code: ZodIssueCode.invalid_string,
              validation: "datetime",
              message: check.message
            });
            status.dirty();
          }
        } else if (check.kind === "date") {
          const regex = dateRegex;
          if (!regex.test(input.data)) {
            ctx = this._getOrReturnCtx(input, ctx);
            addIssueToContext(ctx, {
              code: ZodIssueCode.invalid_string,
              validation: "date",
              message: check.message
            });
            status.dirty();
          }
        } else if (check.kind === "time") {
          const regex = timeRegex(check);
          if (!regex.test(input.data)) {
            ctx = this._getOrReturnCtx(input, ctx);
            addIssueToContext(ctx, {
              code: ZodIssueCode.invalid_string,
              validation: "time",
              message: check.message
            });
            status.dirty();
          }
        } else if (check.kind === "duration") {
          if (!durationRegex.test(input.data)) {
            ctx = this._getOrReturnCtx(input, ctx);
            addIssueToContext(ctx, {
              validation: "duration",
              code: ZodIssueCode.invalid_string,
              message: check.message
            });
            status.dirty();
          }
        } else if (check.kind === "ip") {
          if (!isValidIP(input.data, check.version)) {
            ctx = this._getOrReturnCtx(input, ctx);
            addIssueToContext(ctx, {
              validation: "ip",
              code: ZodIssueCode.invalid_string,
              message: check.message
            });
            status.dirty();
          }
        } else if (check.kind === "jwt") {
          if (!isValidJWT(input.data, check.alg)) {
            ctx = this._getOrReturnCtx(input, ctx);
            addIssueToContext(ctx, {
              validation: "jwt",
              code: ZodIssueCode.invalid_string,
              message: check.message
            });
            status.dirty();
          }
        } else if (check.kind === "cidr") {
          if (!isValidCidr(input.data, check.version)) {
            ctx = this._getOrReturnCtx(input, ctx);
            addIssueToContext(ctx, {
              validation: "cidr",
              code: ZodIssueCode.invalid_string,
              message: check.message
            });
            status.dirty();
          }
        } else if (check.kind === "base64") {
          if (!base64Regex.test(input.data)) {
            ctx = this._getOrReturnCtx(input, ctx);
            addIssueToContext(ctx, {
              validation: "base64",
              code: ZodIssueCode.invalid_string,
              message: check.message
            });
            status.dirty();
          }
        } else if (check.kind === "base64url") {
          if (!base64urlRegex.test(input.data)) {
            ctx = this._getOrReturnCtx(input, ctx);
            addIssueToContext(ctx, {
              validation: "base64url",
              code: ZodIssueCode.invalid_string,
              message: check.message
            });
            status.dirty();
          }
        } else {
          util.assertNever(check);
        }
      }
      return { status: status.value, value: input.data };
    }
    _regex(regex, validation, message) {
      return this.refinement((data) => regex.test(data), {
        validation,
        code: ZodIssueCode.invalid_string,
        ...errorUtil.errToObj(message)
      });
    }
    _addCheck(check) {
      return new _ZodString({
        ...this._def,
        checks: [...this._def.checks, check]
      });
    }
    email(message) {
      return this._addCheck({ kind: "email", ...errorUtil.errToObj(message) });
    }
    url(message) {
      return this._addCheck({ kind: "url", ...errorUtil.errToObj(message) });
    }
    emoji(message) {
      return this._addCheck({ kind: "emoji", ...errorUtil.errToObj(message) });
    }
    uuid(message) {
      return this._addCheck({ kind: "uuid", ...errorUtil.errToObj(message) });
    }
    nanoid(message) {
      return this._addCheck({ kind: "nanoid", ...errorUtil.errToObj(message) });
    }
    cuid(message) {
      return this._addCheck({ kind: "cuid", ...errorUtil.errToObj(message) });
    }
    cuid2(message) {
      return this._addCheck({ kind: "cuid2", ...errorUtil.errToObj(message) });
    }
    ulid(message) {
      return this._addCheck({ kind: "ulid", ...errorUtil.errToObj(message) });
    }
    base64(message) {
      return this._addCheck({ kind: "base64", ...errorUtil.errToObj(message) });
    }
    base64url(message) {
      return this._addCheck({
        kind: "base64url",
        ...errorUtil.errToObj(message)
      });
    }
    jwt(options) {
      return this._addCheck({ kind: "jwt", ...errorUtil.errToObj(options) });
    }
    ip(options) {
      return this._addCheck({ kind: "ip", ...errorUtil.errToObj(options) });
    }
    cidr(options) {
      return this._addCheck({ kind: "cidr", ...errorUtil.errToObj(options) });
    }
    datetime(options) {
      if (typeof options === "string") {
        return this._addCheck({
          kind: "datetime",
          precision: null,
          offset: false,
          local: false,
          message: options
        });
      }
      return this._addCheck({
        kind: "datetime",
        precision: typeof options?.precision === "undefined" ? null : options?.precision,
        offset: options?.offset ?? false,
        local: options?.local ?? false,
        ...errorUtil.errToObj(options?.message)
      });
    }
    date(message) {
      return this._addCheck({ kind: "date", message });
    }
    time(options) {
      if (typeof options === "string") {
        return this._addCheck({
          kind: "time",
          precision: null,
          message: options
        });
      }
      return this._addCheck({
        kind: "time",
        precision: typeof options?.precision === "undefined" ? null : options?.precision,
        ...errorUtil.errToObj(options?.message)
      });
    }
    duration(message) {
      return this._addCheck({ kind: "duration", ...errorUtil.errToObj(message) });
    }
    regex(regex, message) {
      return this._addCheck({
        kind: "regex",
        regex,
        ...errorUtil.errToObj(message)
      });
    }
    includes(value, options) {
      return this._addCheck({
        kind: "includes",
        value,
        position: options?.position,
        ...errorUtil.errToObj(options?.message)
      });
    }
    startsWith(value, message) {
      return this._addCheck({
        kind: "startsWith",
        value,
        ...errorUtil.errToObj(message)
      });
    }
    endsWith(value, message) {
      return this._addCheck({
        kind: "endsWith",
        value,
        ...errorUtil.errToObj(message)
      });
    }
    min(minLength, message) {
      return this._addCheck({
        kind: "min",
        value: minLength,
        ...errorUtil.errToObj(message)
      });
    }
    max(maxLength, message) {
      return this._addCheck({
        kind: "max",
        value: maxLength,
        ...errorUtil.errToObj(message)
      });
    }
    length(len, message) {
      return this._addCheck({
        kind: "length",
        value: len,
        ...errorUtil.errToObj(message)
      });
    }
    /**
     * Equivalent to `.min(1)`
     */
    nonempty(message) {
      return this.min(1, errorUtil.errToObj(message));
    }
    trim() {
      return new _ZodString({
        ...this._def,
        checks: [...this._def.checks, { kind: "trim" }]
      });
    }
    toLowerCase() {
      return new _ZodString({
        ...this._def,
        checks: [...this._def.checks, { kind: "toLowerCase" }]
      });
    }
    toUpperCase() {
      return new _ZodString({
        ...this._def,
        checks: [...this._def.checks, { kind: "toUpperCase" }]
      });
    }
    get isDatetime() {
      return !!this._def.checks.find((ch) => ch.kind === "datetime");
    }
    get isDate() {
      return !!this._def.checks.find((ch) => ch.kind === "date");
    }
    get isTime() {
      return !!this._def.checks.find((ch) => ch.kind === "time");
    }
    get isDuration() {
      return !!this._def.checks.find((ch) => ch.kind === "duration");
    }
    get isEmail() {
      return !!this._def.checks.find((ch) => ch.kind === "email");
    }
    get isURL() {
      return !!this._def.checks.find((ch) => ch.kind === "url");
    }
    get isEmoji() {
      return !!this._def.checks.find((ch) => ch.kind === "emoji");
    }
    get isUUID() {
      return !!this._def.checks.find((ch) => ch.kind === "uuid");
    }
    get isNANOID() {
      return !!this._def.checks.find((ch) => ch.kind === "nanoid");
    }
    get isCUID() {
      return !!this._def.checks.find((ch) => ch.kind === "cuid");
    }
    get isCUID2() {
      return !!this._def.checks.find((ch) => ch.kind === "cuid2");
    }
    get isULID() {
      return !!this._def.checks.find((ch) => ch.kind === "ulid");
    }
    get isIP() {
      return !!this._def.checks.find((ch) => ch.kind === "ip");
    }
    get isCIDR() {
      return !!this._def.checks.find((ch) => ch.kind === "cidr");
    }
    get isBase64() {
      return !!this._def.checks.find((ch) => ch.kind === "base64");
    }
    get isBase64url() {
      return !!this._def.checks.find((ch) => ch.kind === "base64url");
    }
    get minLength() {
      let min = null;
      for (const ch of this._def.checks) {
        if (ch.kind === "min") {
          if (min === null || ch.value > min)
            min = ch.value;
        }
      }
      return min;
    }
    get maxLength() {
      let max = null;
      for (const ch of this._def.checks) {
        if (ch.kind === "max") {
          if (max === null || ch.value < max)
            max = ch.value;
        }
      }
      return max;
    }
  };
  ZodString.create = (params) => {
    return new ZodString({
      checks: [],
      typeName: ZodFirstPartyTypeKind.ZodString,
      coerce: params?.coerce ?? false,
      ...processCreateParams(params)
    });
  };
  function floatSafeRemainder(val, step) {
    const valDecCount = (val.toString().split(".")[1] || "").length;
    const stepDecCount = (step.toString().split(".")[1] || "").length;
    const decCount = valDecCount > stepDecCount ? valDecCount : stepDecCount;
    const valInt = Number.parseInt(val.toFixed(decCount).replace(".", ""));
    const stepInt = Number.parseInt(step.toFixed(decCount).replace(".", ""));
    return valInt % stepInt / 10 ** decCount;
  }
  var ZodNumber = class _ZodNumber extends ZodType {
    constructor() {
      super(...arguments);
      this.min = this.gte;
      this.max = this.lte;
      this.step = this.multipleOf;
    }
    _parse(input) {
      if (this._def.coerce) {
        input.data = Number(input.data);
      }
      const parsedType = this._getType(input);
      if (parsedType !== ZodParsedType.number) {
        const ctx2 = this._getOrReturnCtx(input);
        addIssueToContext(ctx2, {
          code: ZodIssueCode.invalid_type,
          expected: ZodParsedType.number,
          received: ctx2.parsedType
        });
        return INVALID;
      }
      let ctx = void 0;
      const status = new ParseStatus();
      for (const check of this._def.checks) {
        if (check.kind === "int") {
          if (!util.isInteger(input.data)) {
            ctx = this._getOrReturnCtx(input, ctx);
            addIssueToContext(ctx, {
              code: ZodIssueCode.invalid_type,
              expected: "integer",
              received: "float",
              message: check.message
            });
            status.dirty();
          }
        } else if (check.kind === "min") {
          const tooSmall = check.inclusive ? input.data < check.value : input.data <= check.value;
          if (tooSmall) {
            ctx = this._getOrReturnCtx(input, ctx);
            addIssueToContext(ctx, {
              code: ZodIssueCode.too_small,
              minimum: check.value,
              type: "number",
              inclusive: check.inclusive,
              exact: false,
              message: check.message
            });
            status.dirty();
          }
        } else if (check.kind === "max") {
          const tooBig = check.inclusive ? input.data > check.value : input.data >= check.value;
          if (tooBig) {
            ctx = this._getOrReturnCtx(input, ctx);
            addIssueToContext(ctx, {
              code: ZodIssueCode.too_big,
              maximum: check.value,
              type: "number",
              inclusive: check.inclusive,
              exact: false,
              message: check.message
            });
            status.dirty();
          }
        } else if (check.kind === "multipleOf") {
          if (floatSafeRemainder(input.data, check.value) !== 0) {
            ctx = this._getOrReturnCtx(input, ctx);
            addIssueToContext(ctx, {
              code: ZodIssueCode.not_multiple_of,
              multipleOf: check.value,
              message: check.message
            });
            status.dirty();
          }
        } else if (check.kind === "finite") {
          if (!Number.isFinite(input.data)) {
            ctx = this._getOrReturnCtx(input, ctx);
            addIssueToContext(ctx, {
              code: ZodIssueCode.not_finite,
              message: check.message
            });
            status.dirty();
          }
        } else {
          util.assertNever(check);
        }
      }
      return { status: status.value, value: input.data };
    }
    gte(value, message) {
      return this.setLimit("min", value, true, errorUtil.toString(message));
    }
    gt(value, message) {
      return this.setLimit("min", value, false, errorUtil.toString(message));
    }
    lte(value, message) {
      return this.setLimit("max", value, true, errorUtil.toString(message));
    }
    lt(value, message) {
      return this.setLimit("max", value, false, errorUtil.toString(message));
    }
    setLimit(kind, value, inclusive, message) {
      return new _ZodNumber({
        ...this._def,
        checks: [
          ...this._def.checks,
          {
            kind,
            value,
            inclusive,
            message: errorUtil.toString(message)
          }
        ]
      });
    }
    _addCheck(check) {
      return new _ZodNumber({
        ...this._def,
        checks: [...this._def.checks, check]
      });
    }
    int(message) {
      return this._addCheck({
        kind: "int",
        message: errorUtil.toString(message)
      });
    }
    positive(message) {
      return this._addCheck({
        kind: "min",
        value: 0,
        inclusive: false,
        message: errorUtil.toString(message)
      });
    }
    negative(message) {
      return this._addCheck({
        kind: "max",
        value: 0,
        inclusive: false,
        message: errorUtil.toString(message)
      });
    }
    nonpositive(message) {
      return this._addCheck({
        kind: "max",
        value: 0,
        inclusive: true,
        message: errorUtil.toString(message)
      });
    }
    nonnegative(message) {
      return this._addCheck({
        kind: "min",
        value: 0,
        inclusive: true,
        message: errorUtil.toString(message)
      });
    }
    multipleOf(value, message) {
      return this._addCheck({
        kind: "multipleOf",
        value,
        message: errorUtil.toString(message)
      });
    }
    finite(message) {
      return this._addCheck({
        kind: "finite",
        message: errorUtil.toString(message)
      });
    }
    safe(message) {
      return this._addCheck({
        kind: "min",
        inclusive: true,
        value: Number.MIN_SAFE_INTEGER,
        message: errorUtil.toString(message)
      })._addCheck({
        kind: "max",
        inclusive: true,
        value: Number.MAX_SAFE_INTEGER,
        message: errorUtil.toString(message)
      });
    }
    get minValue() {
      let min = null;
      for (const ch of this._def.checks) {
        if (ch.kind === "min") {
          if (min === null || ch.value > min)
            min = ch.value;
        }
      }
      return min;
    }
    get maxValue() {
      let max = null;
      for (const ch of this._def.checks) {
        if (ch.kind === "max") {
          if (max === null || ch.value < max)
            max = ch.value;
        }
      }
      return max;
    }
    get isInt() {
      return !!this._def.checks.find((ch) => ch.kind === "int" || ch.kind === "multipleOf" && util.isInteger(ch.value));
    }
    get isFinite() {
      let max = null;
      let min = null;
      for (const ch of this._def.checks) {
        if (ch.kind === "finite" || ch.kind === "int" || ch.kind === "multipleOf") {
          return true;
        } else if (ch.kind === "min") {
          if (min === null || ch.value > min)
            min = ch.value;
        } else if (ch.kind === "max") {
          if (max === null || ch.value < max)
            max = ch.value;
        }
      }
      return Number.isFinite(min) && Number.isFinite(max);
    }
  };
  ZodNumber.create = (params) => {
    return new ZodNumber({
      checks: [],
      typeName: ZodFirstPartyTypeKind.ZodNumber,
      coerce: params?.coerce || false,
      ...processCreateParams(params)
    });
  };
  var ZodBigInt = class _ZodBigInt extends ZodType {
    constructor() {
      super(...arguments);
      this.min = this.gte;
      this.max = this.lte;
    }
    _parse(input) {
      if (this._def.coerce) {
        try {
          input.data = BigInt(input.data);
        } catch {
          return this._getInvalidInput(input);
        }
      }
      const parsedType = this._getType(input);
      if (parsedType !== ZodParsedType.bigint) {
        return this._getInvalidInput(input);
      }
      let ctx = void 0;
      const status = new ParseStatus();
      for (const check of this._def.checks) {
        if (check.kind === "min") {
          const tooSmall = check.inclusive ? input.data < check.value : input.data <= check.value;
          if (tooSmall) {
            ctx = this._getOrReturnCtx(input, ctx);
            addIssueToContext(ctx, {
              code: ZodIssueCode.too_small,
              type: "bigint",
              minimum: check.value,
              inclusive: check.inclusive,
              message: check.message
            });
            status.dirty();
          }
        } else if (check.kind === "max") {
          const tooBig = check.inclusive ? input.data > check.value : input.data >= check.value;
          if (tooBig) {
            ctx = this._getOrReturnCtx(input, ctx);
            addIssueToContext(ctx, {
              code: ZodIssueCode.too_big,
              type: "bigint",
              maximum: check.value,
              inclusive: check.inclusive,
              message: check.message
            });
            status.dirty();
          }
        } else if (check.kind === "multipleOf") {
          if (input.data % check.value !== BigInt(0)) {
            ctx = this._getOrReturnCtx(input, ctx);
            addIssueToContext(ctx, {
              code: ZodIssueCode.not_multiple_of,
              multipleOf: check.value,
              message: check.message
            });
            status.dirty();
          }
        } else {
          util.assertNever(check);
        }
      }
      return { status: status.value, value: input.data };
    }
    _getInvalidInput(input) {
      const ctx = this._getOrReturnCtx(input);
      addIssueToContext(ctx, {
        code: ZodIssueCode.invalid_type,
        expected: ZodParsedType.bigint,
        received: ctx.parsedType
      });
      return INVALID;
    }
    gte(value, message) {
      return this.setLimit("min", value, true, errorUtil.toString(message));
    }
    gt(value, message) {
      return this.setLimit("min", value, false, errorUtil.toString(message));
    }
    lte(value, message) {
      return this.setLimit("max", value, true, errorUtil.toString(message));
    }
    lt(value, message) {
      return this.setLimit("max", value, false, errorUtil.toString(message));
    }
    setLimit(kind, value, inclusive, message) {
      return new _ZodBigInt({
        ...this._def,
        checks: [
          ...this._def.checks,
          {
            kind,
            value,
            inclusive,
            message: errorUtil.toString(message)
          }
        ]
      });
    }
    _addCheck(check) {
      return new _ZodBigInt({
        ...this._def,
        checks: [...this._def.checks, check]
      });
    }
    positive(message) {
      return this._addCheck({
        kind: "min",
        value: BigInt(0),
        inclusive: false,
        message: errorUtil.toString(message)
      });
    }
    negative(message) {
      return this._addCheck({
        kind: "max",
        value: BigInt(0),
        inclusive: false,
        message: errorUtil.toString(message)
      });
    }
    nonpositive(message) {
      return this._addCheck({
        kind: "max",
        value: BigInt(0),
        inclusive: true,
        message: errorUtil.toString(message)
      });
    }
    nonnegative(message) {
      return this._addCheck({
        kind: "min",
        value: BigInt(0),
        inclusive: true,
        message: errorUtil.toString(message)
      });
    }
    multipleOf(value, message) {
      return this._addCheck({
        kind: "multipleOf",
        value,
        message: errorUtil.toString(message)
      });
    }
    get minValue() {
      let min = null;
      for (const ch of this._def.checks) {
        if (ch.kind === "min") {
          if (min === null || ch.value > min)
            min = ch.value;
        }
      }
      return min;
    }
    get maxValue() {
      let max = null;
      for (const ch of this._def.checks) {
        if (ch.kind === "max") {
          if (max === null || ch.value < max)
            max = ch.value;
        }
      }
      return max;
    }
  };
  ZodBigInt.create = (params) => {
    return new ZodBigInt({
      checks: [],
      typeName: ZodFirstPartyTypeKind.ZodBigInt,
      coerce: params?.coerce ?? false,
      ...processCreateParams(params)
    });
  };
  var ZodBoolean = class extends ZodType {
    _parse(input) {
      if (this._def.coerce) {
        input.data = Boolean(input.data);
      }
      const parsedType = this._getType(input);
      if (parsedType !== ZodParsedType.boolean) {
        const ctx = this._getOrReturnCtx(input);
        addIssueToContext(ctx, {
          code: ZodIssueCode.invalid_type,
          expected: ZodParsedType.boolean,
          received: ctx.parsedType
        });
        return INVALID;
      }
      return OK(input.data);
    }
  };
  ZodBoolean.create = (params) => {
    return new ZodBoolean({
      typeName: ZodFirstPartyTypeKind.ZodBoolean,
      coerce: params?.coerce || false,
      ...processCreateParams(params)
    });
  };
  var ZodDate = class _ZodDate extends ZodType {
    _parse(input) {
      if (this._def.coerce) {
        input.data = new Date(input.data);
      }
      const parsedType = this._getType(input);
      if (parsedType !== ZodParsedType.date) {
        const ctx2 = this._getOrReturnCtx(input);
        addIssueToContext(ctx2, {
          code: ZodIssueCode.invalid_type,
          expected: ZodParsedType.date,
          received: ctx2.parsedType
        });
        return INVALID;
      }
      if (Number.isNaN(input.data.getTime())) {
        const ctx2 = this._getOrReturnCtx(input);
        addIssueToContext(ctx2, {
          code: ZodIssueCode.invalid_date
        });
        return INVALID;
      }
      const status = new ParseStatus();
      let ctx = void 0;
      for (const check of this._def.checks) {
        if (check.kind === "min") {
          if (input.data.getTime() < check.value) {
            ctx = this._getOrReturnCtx(input, ctx);
            addIssueToContext(ctx, {
              code: ZodIssueCode.too_small,
              message: check.message,
              inclusive: true,
              exact: false,
              minimum: check.value,
              type: "date"
            });
            status.dirty();
          }
        } else if (check.kind === "max") {
          if (input.data.getTime() > check.value) {
            ctx = this._getOrReturnCtx(input, ctx);
            addIssueToContext(ctx, {
              code: ZodIssueCode.too_big,
              message: check.message,
              inclusive: true,
              exact: false,
              maximum: check.value,
              type: "date"
            });
            status.dirty();
          }
        } else {
          util.assertNever(check);
        }
      }
      return {
        status: status.value,
        value: new Date(input.data.getTime())
      };
    }
    _addCheck(check) {
      return new _ZodDate({
        ...this._def,
        checks: [...this._def.checks, check]
      });
    }
    min(minDate, message) {
      return this._addCheck({
        kind: "min",
        value: minDate.getTime(),
        message: errorUtil.toString(message)
      });
    }
    max(maxDate, message) {
      return this._addCheck({
        kind: "max",
        value: maxDate.getTime(),
        message: errorUtil.toString(message)
      });
    }
    get minDate() {
      let min = null;
      for (const ch of this._def.checks) {
        if (ch.kind === "min") {
          if (min === null || ch.value > min)
            min = ch.value;
        }
      }
      return min != null ? new Date(min) : null;
    }
    get maxDate() {
      let max = null;
      for (const ch of this._def.checks) {
        if (ch.kind === "max") {
          if (max === null || ch.value < max)
            max = ch.value;
        }
      }
      return max != null ? new Date(max) : null;
    }
  };
  ZodDate.create = (params) => {
    return new ZodDate({
      checks: [],
      coerce: params?.coerce || false,
      typeName: ZodFirstPartyTypeKind.ZodDate,
      ...processCreateParams(params)
    });
  };
  var ZodSymbol = class extends ZodType {
    _parse(input) {
      const parsedType = this._getType(input);
      if (parsedType !== ZodParsedType.symbol) {
        const ctx = this._getOrReturnCtx(input);
        addIssueToContext(ctx, {
          code: ZodIssueCode.invalid_type,
          expected: ZodParsedType.symbol,
          received: ctx.parsedType
        });
        return INVALID;
      }
      return OK(input.data);
    }
  };
  ZodSymbol.create = (params) => {
    return new ZodSymbol({
      typeName: ZodFirstPartyTypeKind.ZodSymbol,
      ...processCreateParams(params)
    });
  };
  var ZodUndefined = class extends ZodType {
    _parse(input) {
      const parsedType = this._getType(input);
      if (parsedType !== ZodParsedType.undefined) {
        const ctx = this._getOrReturnCtx(input);
        addIssueToContext(ctx, {
          code: ZodIssueCode.invalid_type,
          expected: ZodParsedType.undefined,
          received: ctx.parsedType
        });
        return INVALID;
      }
      return OK(input.data);
    }
  };
  ZodUndefined.create = (params) => {
    return new ZodUndefined({
      typeName: ZodFirstPartyTypeKind.ZodUndefined,
      ...processCreateParams(params)
    });
  };
  var ZodNull = class extends ZodType {
    _parse(input) {
      const parsedType = this._getType(input);
      if (parsedType !== ZodParsedType.null) {
        const ctx = this._getOrReturnCtx(input);
        addIssueToContext(ctx, {
          code: ZodIssueCode.invalid_type,
          expected: ZodParsedType.null,
          received: ctx.parsedType
        });
        return INVALID;
      }
      return OK(input.data);
    }
  };
  ZodNull.create = (params) => {
    return new ZodNull({
      typeName: ZodFirstPartyTypeKind.ZodNull,
      ...processCreateParams(params)
    });
  };
  var ZodAny = class extends ZodType {
    constructor() {
      super(...arguments);
      this._any = true;
    }
    _parse(input) {
      return OK(input.data);
    }
  };
  ZodAny.create = (params) => {
    return new ZodAny({
      typeName: ZodFirstPartyTypeKind.ZodAny,
      ...processCreateParams(params)
    });
  };
  var ZodUnknown = class extends ZodType {
    constructor() {
      super(...arguments);
      this._unknown = true;
    }
    _parse(input) {
      return OK(input.data);
    }
  };
  ZodUnknown.create = (params) => {
    return new ZodUnknown({
      typeName: ZodFirstPartyTypeKind.ZodUnknown,
      ...processCreateParams(params)
    });
  };
  var ZodNever = class extends ZodType {
    _parse(input) {
      const ctx = this._getOrReturnCtx(input);
      addIssueToContext(ctx, {
        code: ZodIssueCode.invalid_type,
        expected: ZodParsedType.never,
        received: ctx.parsedType
      });
      return INVALID;
    }
  };
  ZodNever.create = (params) => {
    return new ZodNever({
      typeName: ZodFirstPartyTypeKind.ZodNever,
      ...processCreateParams(params)
    });
  };
  var ZodVoid = class extends ZodType {
    _parse(input) {
      const parsedType = this._getType(input);
      if (parsedType !== ZodParsedType.undefined) {
        const ctx = this._getOrReturnCtx(input);
        addIssueToContext(ctx, {
          code: ZodIssueCode.invalid_type,
          expected: ZodParsedType.void,
          received: ctx.parsedType
        });
        return INVALID;
      }
      return OK(input.data);
    }
  };
  ZodVoid.create = (params) => {
    return new ZodVoid({
      typeName: ZodFirstPartyTypeKind.ZodVoid,
      ...processCreateParams(params)
    });
  };
  var ZodArray = class _ZodArray extends ZodType {
    _parse(input) {
      const { ctx, status } = this._processInputParams(input);
      const def = this._def;
      if (ctx.parsedType !== ZodParsedType.array) {
        addIssueToContext(ctx, {
          code: ZodIssueCode.invalid_type,
          expected: ZodParsedType.array,
          received: ctx.parsedType
        });
        return INVALID;
      }
      if (def.exactLength !== null) {
        const tooBig = ctx.data.length > def.exactLength.value;
        const tooSmall = ctx.data.length < def.exactLength.value;
        if (tooBig || tooSmall) {
          addIssueToContext(ctx, {
            code: tooBig ? ZodIssueCode.too_big : ZodIssueCode.too_small,
            minimum: tooSmall ? def.exactLength.value : void 0,
            maximum: tooBig ? def.exactLength.value : void 0,
            type: "array",
            inclusive: true,
            exact: true,
            message: def.exactLength.message
          });
          status.dirty();
        }
      }
      if (def.minLength !== null) {
        if (ctx.data.length < def.minLength.value) {
          addIssueToContext(ctx, {
            code: ZodIssueCode.too_small,
            minimum: def.minLength.value,
            type: "array",
            inclusive: true,
            exact: false,
            message: def.minLength.message
          });
          status.dirty();
        }
      }
      if (def.maxLength !== null) {
        if (ctx.data.length > def.maxLength.value) {
          addIssueToContext(ctx, {
            code: ZodIssueCode.too_big,
            maximum: def.maxLength.value,
            type: "array",
            inclusive: true,
            exact: false,
            message: def.maxLength.message
          });
          status.dirty();
        }
      }
      if (ctx.common.async) {
        return Promise.all([...ctx.data].map((item, i) => {
          return def.type._parseAsync(new ParseInputLazyPath(ctx, item, ctx.path, i));
        })).then((result2) => {
          return ParseStatus.mergeArray(status, result2);
        });
      }
      const result = [...ctx.data].map((item, i) => {
        return def.type._parseSync(new ParseInputLazyPath(ctx, item, ctx.path, i));
      });
      return ParseStatus.mergeArray(status, result);
    }
    get element() {
      return this._def.type;
    }
    min(minLength, message) {
      return new _ZodArray({
        ...this._def,
        minLength: { value: minLength, message: errorUtil.toString(message) }
      });
    }
    max(maxLength, message) {
      return new _ZodArray({
        ...this._def,
        maxLength: { value: maxLength, message: errorUtil.toString(message) }
      });
    }
    length(len, message) {
      return new _ZodArray({
        ...this._def,
        exactLength: { value: len, message: errorUtil.toString(message) }
      });
    }
    nonempty(message) {
      return this.min(1, message);
    }
  };
  ZodArray.create = (schema, params) => {
    return new ZodArray({
      type: schema,
      minLength: null,
      maxLength: null,
      exactLength: null,
      typeName: ZodFirstPartyTypeKind.ZodArray,
      ...processCreateParams(params)
    });
  };
  function deepPartialify(schema) {
    if (schema instanceof ZodObject) {
      const newShape = {};
      for (const key in schema.shape) {
        const fieldSchema = schema.shape[key];
        newShape[key] = ZodOptional.create(deepPartialify(fieldSchema));
      }
      return new ZodObject({
        ...schema._def,
        shape: () => newShape
      });
    } else if (schema instanceof ZodArray) {
      return new ZodArray({
        ...schema._def,
        type: deepPartialify(schema.element)
      });
    } else if (schema instanceof ZodOptional) {
      return ZodOptional.create(deepPartialify(schema.unwrap()));
    } else if (schema instanceof ZodNullable) {
      return ZodNullable.create(deepPartialify(schema.unwrap()));
    } else if (schema instanceof ZodTuple) {
      return ZodTuple.create(schema.items.map((item) => deepPartialify(item)));
    } else {
      return schema;
    }
  }
  var ZodObject = class _ZodObject extends ZodType {
    constructor() {
      super(...arguments);
      this._cached = null;
      this.nonstrict = this.passthrough;
      this.augment = this.extend;
    }
    _getCached() {
      if (this._cached !== null)
        return this._cached;
      const shape = this._def.shape();
      const keys = util.objectKeys(shape);
      this._cached = { shape, keys };
      return this._cached;
    }
    _parse(input) {
      const parsedType = this._getType(input);
      if (parsedType !== ZodParsedType.object) {
        const ctx2 = this._getOrReturnCtx(input);
        addIssueToContext(ctx2, {
          code: ZodIssueCode.invalid_type,
          expected: ZodParsedType.object,
          received: ctx2.parsedType
        });
        return INVALID;
      }
      const { status, ctx } = this._processInputParams(input);
      const { shape, keys: shapeKeys } = this._getCached();
      const extraKeys = [];
      if (!(this._def.catchall instanceof ZodNever && this._def.unknownKeys === "strip")) {
        for (const key in ctx.data) {
          if (!shapeKeys.includes(key)) {
            extraKeys.push(key);
          }
        }
      }
      const pairs = [];
      for (const key of shapeKeys) {
        const keyValidator = shape[key];
        const value = ctx.data[key];
        pairs.push({
          key: { status: "valid", value: key },
          value: keyValidator._parse(new ParseInputLazyPath(ctx, value, ctx.path, key)),
          alwaysSet: key in ctx.data
        });
      }
      if (this._def.catchall instanceof ZodNever) {
        const unknownKeys = this._def.unknownKeys;
        if (unknownKeys === "passthrough") {
          for (const key of extraKeys) {
            pairs.push({
              key: { status: "valid", value: key },
              value: { status: "valid", value: ctx.data[key] }
            });
          }
        } else if (unknownKeys === "strict") {
          if (extraKeys.length > 0) {
            addIssueToContext(ctx, {
              code: ZodIssueCode.unrecognized_keys,
              keys: extraKeys
            });
            status.dirty();
          }
        } else if (unknownKeys === "strip") {
        } else {
          throw new Error(`Internal ZodObject error: invalid unknownKeys value.`);
        }
      } else {
        const catchall = this._def.catchall;
        for (const key of extraKeys) {
          const value = ctx.data[key];
          pairs.push({
            key: { status: "valid", value: key },
            value: catchall._parse(
              new ParseInputLazyPath(ctx, value, ctx.path, key)
              //, ctx.child(key), value, getParsedType(value)
            ),
            alwaysSet: key in ctx.data
          });
        }
      }
      if (ctx.common.async) {
        return Promise.resolve().then(async () => {
          const syncPairs = [];
          for (const pair of pairs) {
            const key = await pair.key;
            const value = await pair.value;
            syncPairs.push({
              key,
              value,
              alwaysSet: pair.alwaysSet
            });
          }
          return syncPairs;
        }).then((syncPairs) => {
          return ParseStatus.mergeObjectSync(status, syncPairs);
        });
      } else {
        return ParseStatus.mergeObjectSync(status, pairs);
      }
    }
    get shape() {
      return this._def.shape();
    }
    strict(message) {
      errorUtil.errToObj;
      return new _ZodObject({
        ...this._def,
        unknownKeys: "strict",
        ...message !== void 0 ? {
          errorMap: (issue, ctx) => {
            const defaultError = this._def.errorMap?.(issue, ctx).message ?? ctx.defaultError;
            if (issue.code === "unrecognized_keys")
              return {
                message: errorUtil.errToObj(message).message ?? defaultError
              };
            return {
              message: defaultError
            };
          }
        } : {}
      });
    }
    strip() {
      return new _ZodObject({
        ...this._def,
        unknownKeys: "strip"
      });
    }
    passthrough() {
      return new _ZodObject({
        ...this._def,
        unknownKeys: "passthrough"
      });
    }
    // const AugmentFactory =
    //   <Def extends ZodObjectDef>(def: Def) =>
    //   <Augmentation extends ZodRawShape>(
    //     augmentation: Augmentation
    //   ): ZodObject<
    //     extendShape<ReturnType<Def["shape"]>, Augmentation>,
    //     Def["unknownKeys"],
    //     Def["catchall"]
    //   > => {
    //     return new ZodObject({
    //       ...def,
    //       shape: () => ({
    //         ...def.shape(),
    //         ...augmentation,
    //       }),
    //     }) as any;
    //   };
    extend(augmentation) {
      return new _ZodObject({
        ...this._def,
        shape: () => ({
          ...this._def.shape(),
          ...augmentation
        })
      });
    }
    /**
     * Prior to zod@1.0.12 there was a bug in the
     * inferred type of merged objects. Please
     * upgrade if you are experiencing issues.
     */
    merge(merging) {
      const merged = new _ZodObject({
        unknownKeys: merging._def.unknownKeys,
        catchall: merging._def.catchall,
        shape: () => ({
          ...this._def.shape(),
          ...merging._def.shape()
        }),
        typeName: ZodFirstPartyTypeKind.ZodObject
      });
      return merged;
    }
    // merge<
    //   Incoming extends AnyZodObject,
    //   Augmentation extends Incoming["shape"],
    //   NewOutput extends {
    //     [k in keyof Augmentation | keyof Output]: k extends keyof Augmentation
    //       ? Augmentation[k]["_output"]
    //       : k extends keyof Output
    //       ? Output[k]
    //       : never;
    //   },
    //   NewInput extends {
    //     [k in keyof Augmentation | keyof Input]: k extends keyof Augmentation
    //       ? Augmentation[k]["_input"]
    //       : k extends keyof Input
    //       ? Input[k]
    //       : never;
    //   }
    // >(
    //   merging: Incoming
    // ): ZodObject<
    //   extendShape<T, ReturnType<Incoming["_def"]["shape"]>>,
    //   Incoming["_def"]["unknownKeys"],
    //   Incoming["_def"]["catchall"],
    //   NewOutput,
    //   NewInput
    // > {
    //   const merged: any = new ZodObject({
    //     unknownKeys: merging._def.unknownKeys,
    //     catchall: merging._def.catchall,
    //     shape: () =>
    //       objectUtil.mergeShapes(this._def.shape(), merging._def.shape()),
    //     typeName: ZodFirstPartyTypeKind.ZodObject,
    //   }) as any;
    //   return merged;
    // }
    setKey(key, schema) {
      return this.augment({ [key]: schema });
    }
    // merge<Incoming extends AnyZodObject>(
    //   merging: Incoming
    // ): //ZodObject<T & Incoming["_shape"], UnknownKeys, Catchall> = (merging) => {
    // ZodObject<
    //   extendShape<T, ReturnType<Incoming["_def"]["shape"]>>,
    //   Incoming["_def"]["unknownKeys"],
    //   Incoming["_def"]["catchall"]
    // > {
    //   // const mergedShape = objectUtil.mergeShapes(
    //   //   this._def.shape(),
    //   //   merging._def.shape()
    //   // );
    //   const merged: any = new ZodObject({
    //     unknownKeys: merging._def.unknownKeys,
    //     catchall: merging._def.catchall,
    //     shape: () =>
    //       objectUtil.mergeShapes(this._def.shape(), merging._def.shape()),
    //     typeName: ZodFirstPartyTypeKind.ZodObject,
    //   }) as any;
    //   return merged;
    // }
    catchall(index) {
      return new _ZodObject({
        ...this._def,
        catchall: index
      });
    }
    pick(mask) {
      const shape = {};
      for (const key of util.objectKeys(mask)) {
        if (mask[key] && this.shape[key]) {
          shape[key] = this.shape[key];
        }
      }
      return new _ZodObject({
        ...this._def,
        shape: () => shape
      });
    }
    omit(mask) {
      const shape = {};
      for (const key of util.objectKeys(this.shape)) {
        if (!mask[key]) {
          shape[key] = this.shape[key];
        }
      }
      return new _ZodObject({
        ...this._def,
        shape: () => shape
      });
    }
    /**
     * @deprecated
     */
    deepPartial() {
      return deepPartialify(this);
    }
    partial(mask) {
      const newShape = {};
      for (const key of util.objectKeys(this.shape)) {
        const fieldSchema = this.shape[key];
        if (mask && !mask[key]) {
          newShape[key] = fieldSchema;
        } else {
          newShape[key] = fieldSchema.optional();
        }
      }
      return new _ZodObject({
        ...this._def,
        shape: () => newShape
      });
    }
    required(mask) {
      const newShape = {};
      for (const key of util.objectKeys(this.shape)) {
        if (mask && !mask[key]) {
          newShape[key] = this.shape[key];
        } else {
          const fieldSchema = this.shape[key];
          let newField = fieldSchema;
          while (newField instanceof ZodOptional) {
            newField = newField._def.innerType;
          }
          newShape[key] = newField;
        }
      }
      return new _ZodObject({
        ...this._def,
        shape: () => newShape
      });
    }
    keyof() {
      return createZodEnum(util.objectKeys(this.shape));
    }
  };
  ZodObject.create = (shape, params) => {
    return new ZodObject({
      shape: () => shape,
      unknownKeys: "strip",
      catchall: ZodNever.create(),
      typeName: ZodFirstPartyTypeKind.ZodObject,
      ...processCreateParams(params)
    });
  };
  ZodObject.strictCreate = (shape, params) => {
    return new ZodObject({
      shape: () => shape,
      unknownKeys: "strict",
      catchall: ZodNever.create(),
      typeName: ZodFirstPartyTypeKind.ZodObject,
      ...processCreateParams(params)
    });
  };
  ZodObject.lazycreate = (shape, params) => {
    return new ZodObject({
      shape,
      unknownKeys: "strip",
      catchall: ZodNever.create(),
      typeName: ZodFirstPartyTypeKind.ZodObject,
      ...processCreateParams(params)
    });
  };
  var ZodUnion = class extends ZodType {
    _parse(input) {
      const { ctx } = this._processInputParams(input);
      const options = this._def.options;
      function handleResults(results) {
        for (const result of results) {
          if (result.result.status === "valid") {
            return result.result;
          }
        }
        for (const result of results) {
          if (result.result.status === "dirty") {
            ctx.common.issues.push(...result.ctx.common.issues);
            return result.result;
          }
        }
        const unionErrors = results.map((result) => new ZodError(result.ctx.common.issues));
        addIssueToContext(ctx, {
          code: ZodIssueCode.invalid_union,
          unionErrors
        });
        return INVALID;
      }
      if (ctx.common.async) {
        return Promise.all(options.map(async (option) => {
          const childCtx = {
            ...ctx,
            common: {
              ...ctx.common,
              issues: []
            },
            parent: null
          };
          return {
            result: await option._parseAsync({
              data: ctx.data,
              path: ctx.path,
              parent: childCtx
            }),
            ctx: childCtx
          };
        })).then(handleResults);
      } else {
        let dirty = void 0;
        const issues = [];
        for (const option of options) {
          const childCtx = {
            ...ctx,
            common: {
              ...ctx.common,
              issues: []
            },
            parent: null
          };
          const result = option._parseSync({
            data: ctx.data,
            path: ctx.path,
            parent: childCtx
          });
          if (result.status === "valid") {
            return result;
          } else if (result.status === "dirty" && !dirty) {
            dirty = { result, ctx: childCtx };
          }
          if (childCtx.common.issues.length) {
            issues.push(childCtx.common.issues);
          }
        }
        if (dirty) {
          ctx.common.issues.push(...dirty.ctx.common.issues);
          return dirty.result;
        }
        const unionErrors = issues.map((issues2) => new ZodError(issues2));
        addIssueToContext(ctx, {
          code: ZodIssueCode.invalid_union,
          unionErrors
        });
        return INVALID;
      }
    }
    get options() {
      return this._def.options;
    }
  };
  ZodUnion.create = (types, params) => {
    return new ZodUnion({
      options: types,
      typeName: ZodFirstPartyTypeKind.ZodUnion,
      ...processCreateParams(params)
    });
  };
  var getDiscriminator = (type) => {
    if (type instanceof ZodLazy) {
      return getDiscriminator(type.schema);
    } else if (type instanceof ZodEffects) {
      return getDiscriminator(type.innerType());
    } else if (type instanceof ZodLiteral) {
      return [type.value];
    } else if (type instanceof ZodEnum) {
      return type.options;
    } else if (type instanceof ZodNativeEnum) {
      return util.objectValues(type.enum);
    } else if (type instanceof ZodDefault) {
      return getDiscriminator(type._def.innerType);
    } else if (type instanceof ZodUndefined) {
      return [void 0];
    } else if (type instanceof ZodNull) {
      return [null];
    } else if (type instanceof ZodOptional) {
      return [void 0, ...getDiscriminator(type.unwrap())];
    } else if (type instanceof ZodNullable) {
      return [null, ...getDiscriminator(type.unwrap())];
    } else if (type instanceof ZodBranded) {
      return getDiscriminator(type.unwrap());
    } else if (type instanceof ZodReadonly) {
      return getDiscriminator(type.unwrap());
    } else if (type instanceof ZodCatch) {
      return getDiscriminator(type._def.innerType);
    } else {
      return [];
    }
  };
  var ZodDiscriminatedUnion = class _ZodDiscriminatedUnion extends ZodType {
    _parse(input) {
      const { ctx } = this._processInputParams(input);
      if (ctx.parsedType !== ZodParsedType.object) {
        addIssueToContext(ctx, {
          code: ZodIssueCode.invalid_type,
          expected: ZodParsedType.object,
          received: ctx.parsedType
        });
        return INVALID;
      }
      const discriminator = this.discriminator;
      const discriminatorValue = ctx.data[discriminator];
      const option = this.optionsMap.get(discriminatorValue);
      if (!option) {
        addIssueToContext(ctx, {
          code: ZodIssueCode.invalid_union_discriminator,
          options: Array.from(this.optionsMap.keys()),
          path: [discriminator]
        });
        return INVALID;
      }
      if (ctx.common.async) {
        return option._parseAsync({
          data: ctx.data,
          path: ctx.path,
          parent: ctx
        });
      } else {
        return option._parseSync({
          data: ctx.data,
          path: ctx.path,
          parent: ctx
        });
      }
    }
    get discriminator() {
      return this._def.discriminator;
    }
    get options() {
      return this._def.options;
    }
    get optionsMap() {
      return this._def.optionsMap;
    }
    /**
     * The constructor of the discriminated union schema. Its behaviour is very similar to that of the normal z.union() constructor.
     * However, it only allows a union of objects, all of which need to share a discriminator property. This property must
     * have a different value for each object in the union.
     * @param discriminator the name of the discriminator property
     * @param types an array of object schemas
     * @param params
     */
    static create(discriminator, options, params) {
      const optionsMap = /* @__PURE__ */ new Map();
      for (const type of options) {
        const discriminatorValues = getDiscriminator(type.shape[discriminator]);
        if (!discriminatorValues.length) {
          throw new Error(`A discriminator value for key \`${discriminator}\` could not be extracted from all schema options`);
        }
        for (const value of discriminatorValues) {
          if (optionsMap.has(value)) {
            throw new Error(`Discriminator property ${String(discriminator)} has duplicate value ${String(value)}`);
          }
          optionsMap.set(value, type);
        }
      }
      return new _ZodDiscriminatedUnion({
        typeName: ZodFirstPartyTypeKind.ZodDiscriminatedUnion,
        discriminator,
        options,
        optionsMap,
        ...processCreateParams(params)
      });
    }
  };
  function mergeValues(a, b) {
    const aType = getParsedType(a);
    const bType = getParsedType(b);
    if (a === b) {
      return { valid: true, data: a };
    } else if (aType === ZodParsedType.object && bType === ZodParsedType.object) {
      const bKeys = util.objectKeys(b);
      const sharedKeys = util.objectKeys(a).filter((key) => bKeys.indexOf(key) !== -1);
      const newObj = { ...a, ...b };
      for (const key of sharedKeys) {
        const sharedValue = mergeValues(a[key], b[key]);
        if (!sharedValue.valid) {
          return { valid: false };
        }
        newObj[key] = sharedValue.data;
      }
      return { valid: true, data: newObj };
    } else if (aType === ZodParsedType.array && bType === ZodParsedType.array) {
      if (a.length !== b.length) {
        return { valid: false };
      }
      const newArray = [];
      for (let index = 0; index < a.length; index++) {
        const itemA = a[index];
        const itemB = b[index];
        const sharedValue = mergeValues(itemA, itemB);
        if (!sharedValue.valid) {
          return { valid: false };
        }
        newArray.push(sharedValue.data);
      }
      return { valid: true, data: newArray };
    } else if (aType === ZodParsedType.date && bType === ZodParsedType.date && +a === +b) {
      return { valid: true, data: a };
    } else {
      return { valid: false };
    }
  }
  var ZodIntersection = class extends ZodType {
    _parse(input) {
      const { status, ctx } = this._processInputParams(input);
      const handleParsed = (parsedLeft, parsedRight) => {
        if (isAborted(parsedLeft) || isAborted(parsedRight)) {
          return INVALID;
        }
        const merged = mergeValues(parsedLeft.value, parsedRight.value);
        if (!merged.valid) {
          addIssueToContext(ctx, {
            code: ZodIssueCode.invalid_intersection_types
          });
          return INVALID;
        }
        if (isDirty(parsedLeft) || isDirty(parsedRight)) {
          status.dirty();
        }
        return { status: status.value, value: merged.data };
      };
      if (ctx.common.async) {
        return Promise.all([
          this._def.left._parseAsync({
            data: ctx.data,
            path: ctx.path,
            parent: ctx
          }),
          this._def.right._parseAsync({
            data: ctx.data,
            path: ctx.path,
            parent: ctx
          })
        ]).then(([left, right]) => handleParsed(left, right));
      } else {
        return handleParsed(this._def.left._parseSync({
          data: ctx.data,
          path: ctx.path,
          parent: ctx
        }), this._def.right._parseSync({
          data: ctx.data,
          path: ctx.path,
          parent: ctx
        }));
      }
    }
  };
  ZodIntersection.create = (left, right, params) => {
    return new ZodIntersection({
      left,
      right,
      typeName: ZodFirstPartyTypeKind.ZodIntersection,
      ...processCreateParams(params)
    });
  };
  var ZodTuple = class _ZodTuple extends ZodType {
    _parse(input) {
      const { status, ctx } = this._processInputParams(input);
      if (ctx.parsedType !== ZodParsedType.array) {
        addIssueToContext(ctx, {
          code: ZodIssueCode.invalid_type,
          expected: ZodParsedType.array,
          received: ctx.parsedType
        });
        return INVALID;
      }
      if (ctx.data.length < this._def.items.length) {
        addIssueToContext(ctx, {
          code: ZodIssueCode.too_small,
          minimum: this._def.items.length,
          inclusive: true,
          exact: false,
          type: "array"
        });
        return INVALID;
      }
      const rest = this._def.rest;
      if (!rest && ctx.data.length > this._def.items.length) {
        addIssueToContext(ctx, {
          code: ZodIssueCode.too_big,
          maximum: this._def.items.length,
          inclusive: true,
          exact: false,
          type: "array"
        });
        status.dirty();
      }
      const items = [...ctx.data].map((item, itemIndex) => {
        const schema = this._def.items[itemIndex] || this._def.rest;
        if (!schema)
          return null;
        return schema._parse(new ParseInputLazyPath(ctx, item, ctx.path, itemIndex));
      }).filter((x) => !!x);
      if (ctx.common.async) {
        return Promise.all(items).then((results) => {
          return ParseStatus.mergeArray(status, results);
        });
      } else {
        return ParseStatus.mergeArray(status, items);
      }
    }
    get items() {
      return this._def.items;
    }
    rest(rest) {
      return new _ZodTuple({
        ...this._def,
        rest
      });
    }
  };
  ZodTuple.create = (schemas, params) => {
    if (!Array.isArray(schemas)) {
      throw new Error("You must pass an array of schemas to z.tuple([ ... ])");
    }
    return new ZodTuple({
      items: schemas,
      typeName: ZodFirstPartyTypeKind.ZodTuple,
      rest: null,
      ...processCreateParams(params)
    });
  };
  var ZodRecord = class _ZodRecord extends ZodType {
    get keySchema() {
      return this._def.keyType;
    }
    get valueSchema() {
      return this._def.valueType;
    }
    _parse(input) {
      const { status, ctx } = this._processInputParams(input);
      if (ctx.parsedType !== ZodParsedType.object) {
        addIssueToContext(ctx, {
          code: ZodIssueCode.invalid_type,
          expected: ZodParsedType.object,
          received: ctx.parsedType
        });
        return INVALID;
      }
      const pairs = [];
      const keyType = this._def.keyType;
      const valueType = this._def.valueType;
      for (const key in ctx.data) {
        pairs.push({
          key: keyType._parse(new ParseInputLazyPath(ctx, key, ctx.path, key)),
          value: valueType._parse(new ParseInputLazyPath(ctx, ctx.data[key], ctx.path, key)),
          alwaysSet: key in ctx.data
        });
      }
      if (ctx.common.async) {
        return ParseStatus.mergeObjectAsync(status, pairs);
      } else {
        return ParseStatus.mergeObjectSync(status, pairs);
      }
    }
    get element() {
      return this._def.valueType;
    }
    static create(first, second, third) {
      if (second instanceof ZodType) {
        return new _ZodRecord({
          keyType: first,
          valueType: second,
          typeName: ZodFirstPartyTypeKind.ZodRecord,
          ...processCreateParams(third)
        });
      }
      return new _ZodRecord({
        keyType: ZodString.create(),
        valueType: first,
        typeName: ZodFirstPartyTypeKind.ZodRecord,
        ...processCreateParams(second)
      });
    }
  };
  var ZodMap = class extends ZodType {
    get keySchema() {
      return this._def.keyType;
    }
    get valueSchema() {
      return this._def.valueType;
    }
    _parse(input) {
      const { status, ctx } = this._processInputParams(input);
      if (ctx.parsedType !== ZodParsedType.map) {
        addIssueToContext(ctx, {
          code: ZodIssueCode.invalid_type,
          expected: ZodParsedType.map,
          received: ctx.parsedType
        });
        return INVALID;
      }
      const keyType = this._def.keyType;
      const valueType = this._def.valueType;
      const pairs = [...ctx.data.entries()].map(([key, value], index) => {
        return {
          key: keyType._parse(new ParseInputLazyPath(ctx, key, ctx.path, [index, "key"])),
          value: valueType._parse(new ParseInputLazyPath(ctx, value, ctx.path, [index, "value"]))
        };
      });
      if (ctx.common.async) {
        const finalMap = /* @__PURE__ */ new Map();
        return Promise.resolve().then(async () => {
          for (const pair of pairs) {
            const key = await pair.key;
            const value = await pair.value;
            if (key.status === "aborted" || value.status === "aborted") {
              return INVALID;
            }
            if (key.status === "dirty" || value.status === "dirty") {
              status.dirty();
            }
            finalMap.set(key.value, value.value);
          }
          return { status: status.value, value: finalMap };
        });
      } else {
        const finalMap = /* @__PURE__ */ new Map();
        for (const pair of pairs) {
          const key = pair.key;
          const value = pair.value;
          if (key.status === "aborted" || value.status === "aborted") {
            return INVALID;
          }
          if (key.status === "dirty" || value.status === "dirty") {
            status.dirty();
          }
          finalMap.set(key.value, value.value);
        }
        return { status: status.value, value: finalMap };
      }
    }
  };
  ZodMap.create = (keyType, valueType, params) => {
    return new ZodMap({
      valueType,
      keyType,
      typeName: ZodFirstPartyTypeKind.ZodMap,
      ...processCreateParams(params)
    });
  };
  var ZodSet = class _ZodSet extends ZodType {
    _parse(input) {
      const { status, ctx } = this._processInputParams(input);
      if (ctx.parsedType !== ZodParsedType.set) {
        addIssueToContext(ctx, {
          code: ZodIssueCode.invalid_type,
          expected: ZodParsedType.set,
          received: ctx.parsedType
        });
        return INVALID;
      }
      const def = this._def;
      if (def.minSize !== null) {
        if (ctx.data.size < def.minSize.value) {
          addIssueToContext(ctx, {
            code: ZodIssueCode.too_small,
            minimum: def.minSize.value,
            type: "set",
            inclusive: true,
            exact: false,
            message: def.minSize.message
          });
          status.dirty();
        }
      }
      if (def.maxSize !== null) {
        if (ctx.data.size > def.maxSize.value) {
          addIssueToContext(ctx, {
            code: ZodIssueCode.too_big,
            maximum: def.maxSize.value,
            type: "set",
            inclusive: true,
            exact: false,
            message: def.maxSize.message
          });
          status.dirty();
        }
      }
      const valueType = this._def.valueType;
      function finalizeSet(elements2) {
        const parsedSet = /* @__PURE__ */ new Set();
        for (const element of elements2) {
          if (element.status === "aborted")
            return INVALID;
          if (element.status === "dirty")
            status.dirty();
          parsedSet.add(element.value);
        }
        return { status: status.value, value: parsedSet };
      }
      const elements = [...ctx.data.values()].map((item, i) => valueType._parse(new ParseInputLazyPath(ctx, item, ctx.path, i)));
      if (ctx.common.async) {
        return Promise.all(elements).then((elements2) => finalizeSet(elements2));
      } else {
        return finalizeSet(elements);
      }
    }
    min(minSize, message) {
      return new _ZodSet({
        ...this._def,
        minSize: { value: minSize, message: errorUtil.toString(message) }
      });
    }
    max(maxSize, message) {
      return new _ZodSet({
        ...this._def,
        maxSize: { value: maxSize, message: errorUtil.toString(message) }
      });
    }
    size(size, message) {
      return this.min(size, message).max(size, message);
    }
    nonempty(message) {
      return this.min(1, message);
    }
  };
  ZodSet.create = (valueType, params) => {
    return new ZodSet({
      valueType,
      minSize: null,
      maxSize: null,
      typeName: ZodFirstPartyTypeKind.ZodSet,
      ...processCreateParams(params)
    });
  };
  var ZodFunction = class _ZodFunction extends ZodType {
    constructor() {
      super(...arguments);
      this.validate = this.implement;
    }
    _parse(input) {
      const { ctx } = this._processInputParams(input);
      if (ctx.parsedType !== ZodParsedType.function) {
        addIssueToContext(ctx, {
          code: ZodIssueCode.invalid_type,
          expected: ZodParsedType.function,
          received: ctx.parsedType
        });
        return INVALID;
      }
      function makeArgsIssue(args, error) {
        return makeIssue({
          data: args,
          path: ctx.path,
          errorMaps: [ctx.common.contextualErrorMap, ctx.schemaErrorMap, getErrorMap(), en_default].filter((x) => !!x),
          issueData: {
            code: ZodIssueCode.invalid_arguments,
            argumentsError: error
          }
        });
      }
      function makeReturnsIssue(returns, error) {
        return makeIssue({
          data: returns,
          path: ctx.path,
          errorMaps: [ctx.common.contextualErrorMap, ctx.schemaErrorMap, getErrorMap(), en_default].filter((x) => !!x),
          issueData: {
            code: ZodIssueCode.invalid_return_type,
            returnTypeError: error
          }
        });
      }
      const params = { errorMap: ctx.common.contextualErrorMap };
      const fn = ctx.data;
      if (this._def.returns instanceof ZodPromise) {
        const me = this;
        return OK(async function(...args) {
          const error = new ZodError([]);
          const parsedArgs = await me._def.args.parseAsync(args, params).catch((e) => {
            error.addIssue(makeArgsIssue(args, e));
            throw error;
          });
          const result = await Reflect.apply(fn, this, parsedArgs);
          const parsedReturns = await me._def.returns._def.type.parseAsync(result, params).catch((e) => {
            error.addIssue(makeReturnsIssue(result, e));
            throw error;
          });
          return parsedReturns;
        });
      } else {
        const me = this;
        return OK(function(...args) {
          const parsedArgs = me._def.args.safeParse(args, params);
          if (!parsedArgs.success) {
            throw new ZodError([makeArgsIssue(args, parsedArgs.error)]);
          }
          const result = Reflect.apply(fn, this, parsedArgs.data);
          const parsedReturns = me._def.returns.safeParse(result, params);
          if (!parsedReturns.success) {
            throw new ZodError([makeReturnsIssue(result, parsedReturns.error)]);
          }
          return parsedReturns.data;
        });
      }
    }
    parameters() {
      return this._def.args;
    }
    returnType() {
      return this._def.returns;
    }
    args(...items) {
      return new _ZodFunction({
        ...this._def,
        args: ZodTuple.create(items).rest(ZodUnknown.create())
      });
    }
    returns(returnType) {
      return new _ZodFunction({
        ...this._def,
        returns: returnType
      });
    }
    implement(func) {
      const validatedFunc = this.parse(func);
      return validatedFunc;
    }
    strictImplement(func) {
      const validatedFunc = this.parse(func);
      return validatedFunc;
    }
    static create(args, returns, params) {
      return new _ZodFunction({
        args: args ? args : ZodTuple.create([]).rest(ZodUnknown.create()),
        returns: returns || ZodUnknown.create(),
        typeName: ZodFirstPartyTypeKind.ZodFunction,
        ...processCreateParams(params)
      });
    }
  };
  var ZodLazy = class extends ZodType {
    get schema() {
      return this._def.getter();
    }
    _parse(input) {
      const { ctx } = this._processInputParams(input);
      const lazySchema = this._def.getter();
      return lazySchema._parse({ data: ctx.data, path: ctx.path, parent: ctx });
    }
  };
  ZodLazy.create = (getter, params) => {
    return new ZodLazy({
      getter,
      typeName: ZodFirstPartyTypeKind.ZodLazy,
      ...processCreateParams(params)
    });
  };
  var ZodLiteral = class extends ZodType {
    _parse(input) {
      if (input.data !== this._def.value) {
        const ctx = this._getOrReturnCtx(input);
        addIssueToContext(ctx, {
          received: ctx.data,
          code: ZodIssueCode.invalid_literal,
          expected: this._def.value
        });
        return INVALID;
      }
      return { status: "valid", value: input.data };
    }
    get value() {
      return this._def.value;
    }
  };
  ZodLiteral.create = (value, params) => {
    return new ZodLiteral({
      value,
      typeName: ZodFirstPartyTypeKind.ZodLiteral,
      ...processCreateParams(params)
    });
  };
  function createZodEnum(values, params) {
    return new ZodEnum({
      values,
      typeName: ZodFirstPartyTypeKind.ZodEnum,
      ...processCreateParams(params)
    });
  }
  var ZodEnum = class _ZodEnum extends ZodType {
    _parse(input) {
      if (typeof input.data !== "string") {
        const ctx = this._getOrReturnCtx(input);
        const expectedValues = this._def.values;
        addIssueToContext(ctx, {
          expected: util.joinValues(expectedValues),
          received: ctx.parsedType,
          code: ZodIssueCode.invalid_type
        });
        return INVALID;
      }
      if (!this._cache) {
        this._cache = new Set(this._def.values);
      }
      if (!this._cache.has(input.data)) {
        const ctx = this._getOrReturnCtx(input);
        const expectedValues = this._def.values;
        addIssueToContext(ctx, {
          received: ctx.data,
          code: ZodIssueCode.invalid_enum_value,
          options: expectedValues
        });
        return INVALID;
      }
      return OK(input.data);
    }
    get options() {
      return this._def.values;
    }
    get enum() {
      const enumValues = {};
      for (const val of this._def.values) {
        enumValues[val] = val;
      }
      return enumValues;
    }
    get Values() {
      const enumValues = {};
      for (const val of this._def.values) {
        enumValues[val] = val;
      }
      return enumValues;
    }
    get Enum() {
      const enumValues = {};
      for (const val of this._def.values) {
        enumValues[val] = val;
      }
      return enumValues;
    }
    extract(values, newDef = this._def) {
      return _ZodEnum.create(values, {
        ...this._def,
        ...newDef
      });
    }
    exclude(values, newDef = this._def) {
      return _ZodEnum.create(this.options.filter((opt) => !values.includes(opt)), {
        ...this._def,
        ...newDef
      });
    }
  };
  ZodEnum.create = createZodEnum;
  var ZodNativeEnum = class extends ZodType {
    _parse(input) {
      const nativeEnumValues = util.getValidEnumValues(this._def.values);
      const ctx = this._getOrReturnCtx(input);
      if (ctx.parsedType !== ZodParsedType.string && ctx.parsedType !== ZodParsedType.number) {
        const expectedValues = util.objectValues(nativeEnumValues);
        addIssueToContext(ctx, {
          expected: util.joinValues(expectedValues),
          received: ctx.parsedType,
          code: ZodIssueCode.invalid_type
        });
        return INVALID;
      }
      if (!this._cache) {
        this._cache = new Set(util.getValidEnumValues(this._def.values));
      }
      if (!this._cache.has(input.data)) {
        const expectedValues = util.objectValues(nativeEnumValues);
        addIssueToContext(ctx, {
          received: ctx.data,
          code: ZodIssueCode.invalid_enum_value,
          options: expectedValues
        });
        return INVALID;
      }
      return OK(input.data);
    }
    get enum() {
      return this._def.values;
    }
  };
  ZodNativeEnum.create = (values, params) => {
    return new ZodNativeEnum({
      values,
      typeName: ZodFirstPartyTypeKind.ZodNativeEnum,
      ...processCreateParams(params)
    });
  };
  var ZodPromise = class extends ZodType {
    unwrap() {
      return this._def.type;
    }
    _parse(input) {
      const { ctx } = this._processInputParams(input);
      if (ctx.parsedType !== ZodParsedType.promise && ctx.common.async === false) {
        addIssueToContext(ctx, {
          code: ZodIssueCode.invalid_type,
          expected: ZodParsedType.promise,
          received: ctx.parsedType
        });
        return INVALID;
      }
      const promisified = ctx.parsedType === ZodParsedType.promise ? ctx.data : Promise.resolve(ctx.data);
      return OK(promisified.then((data) => {
        return this._def.type.parseAsync(data, {
          path: ctx.path,
          errorMap: ctx.common.contextualErrorMap
        });
      }));
    }
  };
  ZodPromise.create = (schema, params) => {
    return new ZodPromise({
      type: schema,
      typeName: ZodFirstPartyTypeKind.ZodPromise,
      ...processCreateParams(params)
    });
  };
  var ZodEffects = class extends ZodType {
    innerType() {
      return this._def.schema;
    }
    sourceType() {
      return this._def.schema._def.typeName === ZodFirstPartyTypeKind.ZodEffects ? this._def.schema.sourceType() : this._def.schema;
    }
    _parse(input) {
      const { status, ctx } = this._processInputParams(input);
      const effect = this._def.effect || null;
      const checkCtx = {
        addIssue: (arg) => {
          addIssueToContext(ctx, arg);
          if (arg.fatal) {
            status.abort();
          } else {
            status.dirty();
          }
        },
        get path() {
          return ctx.path;
        }
      };
      checkCtx.addIssue = checkCtx.addIssue.bind(checkCtx);
      if (effect.type === "preprocess") {
        const processed = effect.transform(ctx.data, checkCtx);
        if (ctx.common.async) {
          return Promise.resolve(processed).then(async (processed2) => {
            if (status.value === "aborted")
              return INVALID;
            const result = await this._def.schema._parseAsync({
              data: processed2,
              path: ctx.path,
              parent: ctx
            });
            if (result.status === "aborted")
              return INVALID;
            if (result.status === "dirty")
              return DIRTY(result.value);
            if (status.value === "dirty")
              return DIRTY(result.value);
            return result;
          });
        } else {
          if (status.value === "aborted")
            return INVALID;
          const result = this._def.schema._parseSync({
            data: processed,
            path: ctx.path,
            parent: ctx
          });
          if (result.status === "aborted")
            return INVALID;
          if (result.status === "dirty")
            return DIRTY(result.value);
          if (status.value === "dirty")
            return DIRTY(result.value);
          return result;
        }
      }
      if (effect.type === "refinement") {
        const executeRefinement = (acc) => {
          const result = effect.refinement(acc, checkCtx);
          if (ctx.common.async) {
            return Promise.resolve(result);
          }
          if (result instanceof Promise) {
            throw new Error("Async refinement encountered during synchronous parse operation. Use .parseAsync instead.");
          }
          return acc;
        };
        if (ctx.common.async === false) {
          const inner = this._def.schema._parseSync({
            data: ctx.data,
            path: ctx.path,
            parent: ctx
          });
          if (inner.status === "aborted")
            return INVALID;
          if (inner.status === "dirty")
            status.dirty();
          executeRefinement(inner.value);
          return { status: status.value, value: inner.value };
        } else {
          return this._def.schema._parseAsync({ data: ctx.data, path: ctx.path, parent: ctx }).then((inner) => {
            if (inner.status === "aborted")
              return INVALID;
            if (inner.status === "dirty")
              status.dirty();
            return executeRefinement(inner.value).then(() => {
              return { status: status.value, value: inner.value };
            });
          });
        }
      }
      if (effect.type === "transform") {
        if (ctx.common.async === false) {
          const base2 = this._def.schema._parseSync({
            data: ctx.data,
            path: ctx.path,
            parent: ctx
          });
          if (!isValid(base2))
            return INVALID;
          const result = effect.transform(base2.value, checkCtx);
          if (result instanceof Promise) {
            throw new Error(`Asynchronous transform encountered during synchronous parse operation. Use .parseAsync instead.`);
          }
          return { status: status.value, value: result };
        } else {
          return this._def.schema._parseAsync({ data: ctx.data, path: ctx.path, parent: ctx }).then((base2) => {
            if (!isValid(base2))
              return INVALID;
            return Promise.resolve(effect.transform(base2.value, checkCtx)).then((result) => ({
              status: status.value,
              value: result
            }));
          });
        }
      }
      util.assertNever(effect);
    }
  };
  ZodEffects.create = (schema, effect, params) => {
    return new ZodEffects({
      schema,
      typeName: ZodFirstPartyTypeKind.ZodEffects,
      effect,
      ...processCreateParams(params)
    });
  };
  ZodEffects.createWithPreprocess = (preprocess, schema, params) => {
    return new ZodEffects({
      schema,
      effect: { type: "preprocess", transform: preprocess },
      typeName: ZodFirstPartyTypeKind.ZodEffects,
      ...processCreateParams(params)
    });
  };
  var ZodOptional = class extends ZodType {
    _parse(input) {
      const parsedType = this._getType(input);
      if (parsedType === ZodParsedType.undefined) {
        return OK(void 0);
      }
      return this._def.innerType._parse(input);
    }
    unwrap() {
      return this._def.innerType;
    }
  };
  ZodOptional.create = (type, params) => {
    return new ZodOptional({
      innerType: type,
      typeName: ZodFirstPartyTypeKind.ZodOptional,
      ...processCreateParams(params)
    });
  };
  var ZodNullable = class extends ZodType {
    _parse(input) {
      const parsedType = this._getType(input);
      if (parsedType === ZodParsedType.null) {
        return OK(null);
      }
      return this._def.innerType._parse(input);
    }
    unwrap() {
      return this._def.innerType;
    }
  };
  ZodNullable.create = (type, params) => {
    return new ZodNullable({
      innerType: type,
      typeName: ZodFirstPartyTypeKind.ZodNullable,
      ...processCreateParams(params)
    });
  };
  var ZodDefault = class extends ZodType {
    _parse(input) {
      const { ctx } = this._processInputParams(input);
      let data = ctx.data;
      if (ctx.parsedType === ZodParsedType.undefined) {
        data = this._def.defaultValue();
      }
      return this._def.innerType._parse({
        data,
        path: ctx.path,
        parent: ctx
      });
    }
    removeDefault() {
      return this._def.innerType;
    }
  };
  ZodDefault.create = (type, params) => {
    return new ZodDefault({
      innerType: type,
      typeName: ZodFirstPartyTypeKind.ZodDefault,
      defaultValue: typeof params.default === "function" ? params.default : () => params.default,
      ...processCreateParams(params)
    });
  };
  var ZodCatch = class extends ZodType {
    _parse(input) {
      const { ctx } = this._processInputParams(input);
      const newCtx = {
        ...ctx,
        common: {
          ...ctx.common,
          issues: []
        }
      };
      const result = this._def.innerType._parse({
        data: newCtx.data,
        path: newCtx.path,
        parent: {
          ...newCtx
        }
      });
      if (isAsync(result)) {
        return result.then((result2) => {
          return {
            status: "valid",
            value: result2.status === "valid" ? result2.value : this._def.catchValue({
              get error() {
                return new ZodError(newCtx.common.issues);
              },
              input: newCtx.data
            })
          };
        });
      } else {
        return {
          status: "valid",
          value: result.status === "valid" ? result.value : this._def.catchValue({
            get error() {
              return new ZodError(newCtx.common.issues);
            },
            input: newCtx.data
          })
        };
      }
    }
    removeCatch() {
      return this._def.innerType;
    }
  };
  ZodCatch.create = (type, params) => {
    return new ZodCatch({
      innerType: type,
      typeName: ZodFirstPartyTypeKind.ZodCatch,
      catchValue: typeof params.catch === "function" ? params.catch : () => params.catch,
      ...processCreateParams(params)
    });
  };
  var ZodNaN = class extends ZodType {
    _parse(input) {
      const parsedType = this._getType(input);
      if (parsedType !== ZodParsedType.nan) {
        const ctx = this._getOrReturnCtx(input);
        addIssueToContext(ctx, {
          code: ZodIssueCode.invalid_type,
          expected: ZodParsedType.nan,
          received: ctx.parsedType
        });
        return INVALID;
      }
      return { status: "valid", value: input.data };
    }
  };
  ZodNaN.create = (params) => {
    return new ZodNaN({
      typeName: ZodFirstPartyTypeKind.ZodNaN,
      ...processCreateParams(params)
    });
  };
  var BRAND = /* @__PURE__ */ Symbol("zod_brand");
  var ZodBranded = class extends ZodType {
    _parse(input) {
      const { ctx } = this._processInputParams(input);
      const data = ctx.data;
      return this._def.type._parse({
        data,
        path: ctx.path,
        parent: ctx
      });
    }
    unwrap() {
      return this._def.type;
    }
  };
  var ZodPipeline = class _ZodPipeline extends ZodType {
    _parse(input) {
      const { status, ctx } = this._processInputParams(input);
      if (ctx.common.async) {
        const handleAsync = async () => {
          const inResult = await this._def.in._parseAsync({
            data: ctx.data,
            path: ctx.path,
            parent: ctx
          });
          if (inResult.status === "aborted")
            return INVALID;
          if (inResult.status === "dirty") {
            status.dirty();
            return DIRTY(inResult.value);
          } else {
            return this._def.out._parseAsync({
              data: inResult.value,
              path: ctx.path,
              parent: ctx
            });
          }
        };
        return handleAsync();
      } else {
        const inResult = this._def.in._parseSync({
          data: ctx.data,
          path: ctx.path,
          parent: ctx
        });
        if (inResult.status === "aborted")
          return INVALID;
        if (inResult.status === "dirty") {
          status.dirty();
          return {
            status: "dirty",
            value: inResult.value
          };
        } else {
          return this._def.out._parseSync({
            data: inResult.value,
            path: ctx.path,
            parent: ctx
          });
        }
      }
    }
    static create(a, b) {
      return new _ZodPipeline({
        in: a,
        out: b,
        typeName: ZodFirstPartyTypeKind.ZodPipeline
      });
    }
  };
  var ZodReadonly = class extends ZodType {
    _parse(input) {
      const result = this._def.innerType._parse(input);
      const freeze = (data) => {
        if (isValid(data)) {
          data.value = Object.freeze(data.value);
        }
        return data;
      };
      return isAsync(result) ? result.then((data) => freeze(data)) : freeze(result);
    }
    unwrap() {
      return this._def.innerType;
    }
  };
  ZodReadonly.create = (type, params) => {
    return new ZodReadonly({
      innerType: type,
      typeName: ZodFirstPartyTypeKind.ZodReadonly,
      ...processCreateParams(params)
    });
  };
  function cleanParams(params, data) {
    const p = typeof params === "function" ? params(data) : typeof params === "string" ? { message: params } : params;
    const p2 = typeof p === "string" ? { message: p } : p;
    return p2;
  }
  function custom(check, _params = {}, fatal) {
    if (check)
      return ZodAny.create().superRefine((data, ctx) => {
        const r = check(data);
        if (r instanceof Promise) {
          return r.then((r2) => {
            if (!r2) {
              const params = cleanParams(_params, data);
              const _fatal = params.fatal ?? fatal ?? true;
              ctx.addIssue({ code: "custom", ...params, fatal: _fatal });
            }
          });
        }
        if (!r) {
          const params = cleanParams(_params, data);
          const _fatal = params.fatal ?? fatal ?? true;
          ctx.addIssue({ code: "custom", ...params, fatal: _fatal });
        }
        return;
      });
    return ZodAny.create();
  }
  var late = {
    object: ZodObject.lazycreate
  };
  var ZodFirstPartyTypeKind;
  (function(ZodFirstPartyTypeKind2) {
    ZodFirstPartyTypeKind2["ZodString"] = "ZodString";
    ZodFirstPartyTypeKind2["ZodNumber"] = "ZodNumber";
    ZodFirstPartyTypeKind2["ZodNaN"] = "ZodNaN";
    ZodFirstPartyTypeKind2["ZodBigInt"] = "ZodBigInt";
    ZodFirstPartyTypeKind2["ZodBoolean"] = "ZodBoolean";
    ZodFirstPartyTypeKind2["ZodDate"] = "ZodDate";
    ZodFirstPartyTypeKind2["ZodSymbol"] = "ZodSymbol";
    ZodFirstPartyTypeKind2["ZodUndefined"] = "ZodUndefined";
    ZodFirstPartyTypeKind2["ZodNull"] = "ZodNull";
    ZodFirstPartyTypeKind2["ZodAny"] = "ZodAny";
    ZodFirstPartyTypeKind2["ZodUnknown"] = "ZodUnknown";
    ZodFirstPartyTypeKind2["ZodNever"] = "ZodNever";
    ZodFirstPartyTypeKind2["ZodVoid"] = "ZodVoid";
    ZodFirstPartyTypeKind2["ZodArray"] = "ZodArray";
    ZodFirstPartyTypeKind2["ZodObject"] = "ZodObject";
    ZodFirstPartyTypeKind2["ZodUnion"] = "ZodUnion";
    ZodFirstPartyTypeKind2["ZodDiscriminatedUnion"] = "ZodDiscriminatedUnion";
    ZodFirstPartyTypeKind2["ZodIntersection"] = "ZodIntersection";
    ZodFirstPartyTypeKind2["ZodTuple"] = "ZodTuple";
    ZodFirstPartyTypeKind2["ZodRecord"] = "ZodRecord";
    ZodFirstPartyTypeKind2["ZodMap"] = "ZodMap";
    ZodFirstPartyTypeKind2["ZodSet"] = "ZodSet";
    ZodFirstPartyTypeKind2["ZodFunction"] = "ZodFunction";
    ZodFirstPartyTypeKind2["ZodLazy"] = "ZodLazy";
    ZodFirstPartyTypeKind2["ZodLiteral"] = "ZodLiteral";
    ZodFirstPartyTypeKind2["ZodEnum"] = "ZodEnum";
    ZodFirstPartyTypeKind2["ZodEffects"] = "ZodEffects";
    ZodFirstPartyTypeKind2["ZodNativeEnum"] = "ZodNativeEnum";
    ZodFirstPartyTypeKind2["ZodOptional"] = "ZodOptional";
    ZodFirstPartyTypeKind2["ZodNullable"] = "ZodNullable";
    ZodFirstPartyTypeKind2["ZodDefault"] = "ZodDefault";
    ZodFirstPartyTypeKind2["ZodCatch"] = "ZodCatch";
    ZodFirstPartyTypeKind2["ZodPromise"] = "ZodPromise";
    ZodFirstPartyTypeKind2["ZodBranded"] = "ZodBranded";
    ZodFirstPartyTypeKind2["ZodPipeline"] = "ZodPipeline";
    ZodFirstPartyTypeKind2["ZodReadonly"] = "ZodReadonly";
  })(ZodFirstPartyTypeKind || (ZodFirstPartyTypeKind = {}));
  var instanceOfType = (cls, params = {
    message: `Input not instance of ${cls.name}`
  }) => custom((data) => data instanceof cls, params);
  var stringType = ZodString.create;
  var numberType = ZodNumber.create;
  var nanType = ZodNaN.create;
  var bigIntType = ZodBigInt.create;
  var booleanType = ZodBoolean.create;
  var dateType = ZodDate.create;
  var symbolType = ZodSymbol.create;
  var undefinedType = ZodUndefined.create;
  var nullType = ZodNull.create;
  var anyType = ZodAny.create;
  var unknownType = ZodUnknown.create;
  var neverType = ZodNever.create;
  var voidType = ZodVoid.create;
  var arrayType = ZodArray.create;
  var objectType = ZodObject.create;
  var strictObjectType = ZodObject.strictCreate;
  var unionType = ZodUnion.create;
  var discriminatedUnionType = ZodDiscriminatedUnion.create;
  var intersectionType = ZodIntersection.create;
  var tupleType = ZodTuple.create;
  var recordType = ZodRecord.create;
  var mapType = ZodMap.create;
  var setType = ZodSet.create;
  var functionType = ZodFunction.create;
  var lazyType = ZodLazy.create;
  var literalType = ZodLiteral.create;
  var enumType = ZodEnum.create;
  var nativeEnumType = ZodNativeEnum.create;
  var promiseType = ZodPromise.create;
  var effectsType = ZodEffects.create;
  var optionalType = ZodOptional.create;
  var nullableType = ZodNullable.create;
  var preprocessType = ZodEffects.createWithPreprocess;
  var pipelineType = ZodPipeline.create;
  var ostring = () => stringType().optional();
  var onumber = () => numberType().optional();
  var oboolean = () => booleanType().optional();
  var coerce = {
    string: ((arg) => ZodString.create({ ...arg, coerce: true })),
    number: ((arg) => ZodNumber.create({ ...arg, coerce: true })),
    boolean: ((arg) => ZodBoolean.create({
      ...arg,
      coerce: true
    })),
    bigint: ((arg) => ZodBigInt.create({ ...arg, coerce: true })),
    date: ((arg) => ZodDate.create({ ...arg, coerce: true }))
  };
  var NEVER = INVALID;

  // data/training-schools.json
  var training_schools_default = {
    source: {
      filename: "2026 Bang tong hop quy m\xF4 tuyen sinh.xlsx",
      sheet: "Mau 1 - KQTS",
      title: "T\u1ED4NG H\u1EE2P B\xC1O C\xC1O K\u1EBET QU\u1EA2 TUY\u1EC2N SINH 9 TH\xC1NG \u0110\u1EA6U N\u0102M 2025",
      periodLabel: "(T\xEDnh \u0111\u1EBFn 9/2025)",
      importedAt: "2026-10-01",
      sha256: "e829c03b42cc864dbad204dfd34cdbb9806596a5c23856aee54294c4c00a83a2",
      notes: [
        "T\xEAn file ghi n\u0103m 2026, ti\xEAu \u0111\u1EC1 v\xE0 th\u1EDDi \u0111i\u1EC3m trong b\u1EA3ng ghi n\u0103m 2025; k\u1EF3 s\u1ED1 li\u1EC7u c\u1EA7n x\xE1c nh\u1EADn.",
        "C\xE1c \xF4 t\u1ED5ng \u1EDF d\xF2ng 16, 17, 214 b\u1ECB l\u1ED7i #REF!; kh\xF4ng s\u1EED d\u1EE5ng l\xE0m t\u1ED5ng h\u1EE3p.",
        "Quy m\xF4 trong b\u1EA3ng ngu\u1ED3n kh\xF4ng t\u1EF1 chuy\u1EC3n th\xE0nh n\u0103ng l\u1EF1c \u0111\xE1p \u1EE9ng theo qu\xFD ho\u1EB7c s\u1ED1 ng\u01B0\u1EDDi s\u1EB5n s\xE0ng l\xE0m vi\u1EC7c."
      ]
    },
    schools: [
      {
        id: "school-r18",
        name: "Tr\u01B0\u1EDDng C\u0110 ngh\u1EC1 s\u1ED1 20/BQP",
        code: "CDT2503",
        type: "Cao \u0111\u1EB3ng",
        sourceRow: 18,
        reported: {
          total: 3050,
          college: 295,
          intermediate: 1065,
          elementary: 1690
        },
        rawCounts: [
          3050,
          295,
          1065,
          1690
        ],
        groups: [],
        programs: [
          {
            id: "program-r19",
            name: "C\xF4ng ngh\u1EC7 \xF4 t\xF4",
            code: "6510216",
            sourceRow: 19,
            levels: [
              "Cao \u0111\u1EB3ng",
              "Trung c\u1EA5p",
              "S\u01A1 c\u1EA5p"
            ],
            reported: {
              total: 560,
              college: 105,
              intermediate: 280,
              elementary: 175
            },
            rawCounts: [
              560,
              105,
              280,
              175
            ]
          },
          {
            id: "program-r20",
            name: "KT l\u1EAFp \u0111\u1EB7t \u0111i\u1EC7n v\xE0 \u0110KTCN",
            code: "6520201",
            sourceRow: 20,
            levels: [
              "Cao \u0111\u1EB3ng",
              "Trung c\u1EA5p"
            ],
            reported: {
              total: 185,
              college: 35,
              intermediate: 150,
              elementary: null
            },
            rawCounts: [
              185,
              35,
              150,
              null
            ]
          },
          {
            id: "program-r21",
            name: "\u0110i\u1EC7n t\u1EED c\xF4ng nghi\u1EC7p",
            code: "6520225",
            sourceRow: 21,
            levels: [
              "Cao \u0111\u1EB3ng",
              "Trung c\u1EA5p",
              "S\u01A1 c\u1EA5p"
            ],
            reported: {
              total: 205,
              college: 35,
              intermediate: 65,
              elementary: 105
            },
            rawCounts: [
              205,
              35,
              65,
              105
            ]
          },
          {
            id: "program-r22",
            name: "K\u1EF9 thu\u1EADt m\xE1y l\u1EA1nh v\xE0 \u0110HKK",
            code: "6520205",
            sourceRow: 22,
            levels: [
              "Cao \u0111\u1EB3ng",
              "Trung c\u1EA5p",
              "S\u01A1 c\u1EA5p"
            ],
            reported: {
              total: 505,
              college: 70,
              intermediate: 225,
              elementary: 210
            },
            rawCounts: [
              505,
              70,
              225,
              210
            ]
          },
          {
            id: "program-r23",
            name: "KT s\u1EEDa ch\u1EEFa, l\u1EAFp r\xE1p m\xE1y t\xEDnh",
            code: "6480102",
            sourceRow: 23,
            levels: [
              "Cao \u0111\u1EB3ng",
              "Trung c\u1EA5p"
            ],
            reported: {
              total: 50,
              college: 25,
              intermediate: 25,
              elementary: null
            },
            rawCounts: [
              50,
              25,
              25,
              null
            ]
          },
          {
            id: "program-r24",
            name: "V\u1EADn h\xE0nh m\xE1y thi c\xF4ng n\u1EC1n",
            code: "6520183",
            sourceRow: 24,
            levels: [
              "Cao \u0111\u1EB3ng",
              "Trung c\u1EA5p",
              "S\u01A1 c\u1EA5p"
            ],
            reported: {
              total: 170,
              college: 25,
              intermediate: 25,
              elementary: 120
            },
            rawCounts: [
              170,
              25,
              25,
              120
            ]
          },
          {
            id: "program-r25",
            name: "May th\u1EDDi trang",
            code: "5540205",
            sourceRow: 25,
            levels: [
              "Trung c\u1EA5p",
              "S\u01A1 c\u1EA5p"
            ],
            reported: {
              total: 330,
              college: null,
              intermediate: 225,
              elementary: 105
            },
            rawCounts: [
              330,
              null,
              225,
              105
            ]
          },
          {
            id: "program-r26",
            name: "S\u1EEDa ch\u1EEFa thi\u1EBFt b\u1ECB may",
            code: "5520133",
            sourceRow: 26,
            levels: [
              "Trung c\u1EA5p"
            ],
            reported: {
              total: 70,
              college: null,
              intermediate: 70,
              elementary: null
            },
            rawCounts: [
              70,
              null,
              70,
              null
            ]
          },
          {
            id: "program-r27",
            name: "\u0110i\u1EC7n c\xF4ng nghi\u1EC7p",
            code: "",
            sourceRow: 27,
            levels: [
              "S\u01A1 c\u1EA5p"
            ],
            reported: {
              total: 130,
              college: null,
              intermediate: null,
              elementary: 130
            },
            rawCounts: [
              130,
              null,
              null,
              130
            ]
          },
          {
            id: "program-r28",
            name: "C\xF4ng ngh\u1EC7 th\xF4ng tin",
            code: "",
            sourceRow: 28,
            levels: [
              "S\u01A1 c\u1EA5p"
            ],
            reported: {
              total: 70,
              college: null,
              intermediate: null,
              elementary: 70
            },
            rawCounts: [
              70,
              null,
              null,
              70
            ]
          },
          {
            id: "program-r29",
            name: "B\u1EA3o tr\xEC m\xE1y may",
            code: "",
            sourceRow: 29,
            levels: [
              "S\u01A1 c\u1EA5p"
            ],
            reported: {
              total: 140,
              college: null,
              intermediate: null,
              elementary: 140
            },
            rawCounts: [
              140,
              null,
              null,
              140
            ]
          },
          {
            id: "program-r30",
            name: "H\xE0n c\xF4ng ngh\u1EC7 cao",
            code: "",
            sourceRow: 30,
            levels: [
              "S\u01A1 c\u1EA5p"
            ],
            reported: {
              total: 155,
              college: null,
              intermediate: null,
              elementary: 155
            },
            rawCounts: [
              155,
              null,
              null,
              155
            ]
          },
          {
            id: "program-r31",
            name: "S\u1EEDa ch\u1EEFa c\u01A1 kh\xED \u0111\u1ED9ng c\u01A1",
            code: "",
            sourceRow: 31,
            levels: [
              "S\u01A1 c\u1EA5p"
            ],
            reported: {
              total: 155,
              college: null,
              intermediate: null,
              elementary: 155
            },
            rawCounts: [
              155,
              null,
              null,
              155
            ]
          },
          {
            id: "program-r32",
            name: "S\u1EEDa ch\u1EEFa \u0111i\u1EC7n - \u0111i\u1EC7n l\u1EA1nh \xF4 t\xF4",
            code: "",
            sourceRow: 32,
            levels: [
              "S\u01A1 c\u1EA5p"
            ],
            reported: {
              total: 175,
              college: null,
              intermediate: null,
              elementary: 175
            },
            rawCounts: [
              175,
              null,
              null,
              175
            ]
          },
          {
            id: "program-r33",
            name: "Ti\u1EBFng H\xE0n Qu\u1ED1c",
            code: "",
            sourceRow: 33,
            levels: [
              "S\u01A1 c\u1EA5p"
            ],
            reported: {
              total: 150,
              college: null,
              intermediate: null,
              elementary: 150
            },
            rawCounts: [
              150,
              null,
              null,
              150
            ]
          },
          {
            id: "program-r34",
            name: "B\u1ED3i d\u01B0\u1EE1ng Ti\u1EBFng Nh\u1EADt",
            code: "",
            sourceRow: 34,
            levels: [],
            reported: {
              total: null,
              college: null,
              intermediate: null,
              elementary: null
            },
            rawCounts: [
              null,
              null,
              null,
              null
            ]
          },
          {
            id: "program-r35",
            name: "L\xE1i xe \xF4 t\xF4 h\u1EA1ng B.01, B, C1",
            code: "",
            sourceRow: 35,
            levels: [],
            reported: {
              total: null,
              college: null,
              intermediate: null,
              elementary: null
            },
            rawCounts: [
              null,
              null,
              null,
              null
            ]
          },
          {
            id: "program-r36",
            name: "B\u1ED3i d\u01B0\u1EE1ng CCCM, GCNKNCM thuy\u1EC1n, m\xE1y tr\u01B0\u1EDFng, LPT",
            code: "",
            sourceRow: 36,
            levels: [],
            reported: {
              total: null,
              college: null,
              intermediate: null,
              elementary: null
            },
            rawCounts: [
              null,
              null,
              null,
              null
            ]
          }
        ],
        issues: []
      },
      {
        id: "school-r37",
        name: "Tr\u01B0\u1EDDng Cao \u0111\u1EB3ng X\xE2y d\u1EF1ng N\u0110",
        code: "CDT2502",
        type: "Cao \u0111\u1EB3ng",
        sourceRow: 37,
        reported: {
          total: 920,
          college: 100,
          intermediate: 690,
          elementary: 130
        },
        rawCounts: [
          920,
          100,
          690,
          130
        ],
        groups: [],
        programs: [
          {
            id: "program-r38",
            name: "C\xF4ng ngh\u1EC7 k\u1EF9 thu\u1EADt C\xF4ng tr\xECnh XD",
            code: "6510104",
            sourceRow: 38,
            levels: [
              "Cao \u0111\u1EB3ng"
            ],
            reported: {
              total: 40,
              college: 40,
              intermediate: null,
              elementary: null
            },
            rawCounts: [
              40,
              40,
              null,
              null
            ]
          },
          {
            id: "program-r39",
            name: "Qu\u1EA3n l\xFD X\xE2y d\u1EF1ng",
            code: "6580301",
            sourceRow: 39,
            levels: [
              "Cao \u0111\u1EB3ng"
            ],
            reported: {
              total: 10,
              college: 10,
              intermediate: null,
              elementary: null
            },
            rawCounts: [
              10,
              10,
              null,
              null
            ]
          },
          {
            id: "program-r40",
            name: "K\u1EBF to\xE1n doanh nghi\u1EC7p",
            code: "6340302",
            sourceRow: 40,
            levels: [
              "Cao \u0111\u1EB3ng"
            ],
            reported: {
              total: 20,
              college: 20,
              intermediate: null,
              elementary: null
            },
            rawCounts: [
              20,
              20,
              null,
              null
            ]
          },
          {
            id: "program-r41",
            name: "H\xE0n",
            code: "6520123",
            sourceRow: 41,
            levels: [
              "Cao \u0111\u1EB3ng"
            ],
            reported: {
              total: 10,
              college: 10,
              intermediate: null,
              elementary: null
            },
            rawCounts: [
              10,
              10,
              null,
              null
            ]
          },
          {
            id: "program-r42",
            name: "K\u1EF9 thu\u1EADt x\xE2y d\u1EF1ng",
            code: "6580201",
            sourceRow: 42,
            levels: [
              "Cao \u0111\u1EB3ng"
            ],
            reported: {
              total: 20,
              college: 20,
              intermediate: null,
              elementary: null
            },
            rawCounts: [
              20,
              20,
              null,
              null
            ]
          },
          {
            id: "program-r43",
            name: "X\xE2y d\u1EF1ng d\xE2n d\u1EE5ng v\xE0 c\xF4ng nghi\u1EC7p",
            code: "5580202",
            sourceRow: 43,
            levels: [
              "Trung c\u1EA5p"
            ],
            reported: {
              total: 60,
              college: null,
              intermediate: 60,
              elementary: null
            },
            rawCounts: [
              60,
              null,
              60,
              null
            ]
          },
          {
            id: "program-r44",
            name: "K\u1EBF to\xE1n doanh nghi\u1EC7p",
            code: "6340302",
            sourceRow: 44,
            levels: [
              "Trung c\u1EA5p"
            ],
            reported: {
              total: 70,
              college: null,
              intermediate: 70,
              elementary: null
            },
            rawCounts: [
              70,
              null,
              70,
              null
            ]
          },
          {
            id: "program-r45",
            name: "Qu\u1EA3n tr\u1ECB m\u1EA1ng m\xE1y t\xEDnh",
            code: "5480209",
            sourceRow: 45,
            levels: [
              "Trung c\u1EA5p"
            ],
            reported: {
              total: 55,
              college: null,
              intermediate: 55,
              elementary: null
            },
            rawCounts: [
              55,
              null,
              55,
              null
            ]
          },
          {
            id: "program-r46",
            name: "Tin h\u1ECDc v\u0103n ph\xF2ng",
            code: "5480203",
            sourceRow: 46,
            levels: [
              "Trung c\u1EA5p"
            ],
            reported: {
              total: 50,
              college: null,
              intermediate: 50,
              elementary: null
            },
            rawCounts: [
              50,
              null,
              50,
              null
            ]
          },
          {
            id: "program-r47",
            name: "\u0110i\u1EC7n d\xE2n d\u1EE5ng",
            code: "5520226",
            sourceRow: 47,
            levels: [
              "Trung c\u1EA5p",
              "S\u01A1 c\u1EA5p"
            ],
            reported: {
              total: 175,
              college: null,
              intermediate: 165,
              elementary: 10
            },
            rawCounts: [
              175,
              null,
              165,
              10
            ]
          },
          {
            id: "program-r48",
            name: "Th\u01B0\u01A1ng m\u1EA1i \u0111i\u1EC7n t\u1EED",
            code: "5340122",
            sourceRow: 48,
            levels: [
              "Trung c\u1EA5p"
            ],
            reported: {
              total: 35,
              college: null,
              intermediate: 35,
              elementary: null
            },
            rawCounts: [
              35,
              null,
              35,
              null
            ]
          },
          {
            id: "program-r49",
            name: "Thi\u1EBFt k\u1EBF \u0111\u1ED3 ho\u1EA1",
            code: "52104028",
            sourceRow: 49,
            levels: [
              "Trung c\u1EA5p"
            ],
            reported: {
              total: 35,
              college: null,
              intermediate: 35,
              elementary: null
            },
            rawCounts: [
              35,
              null,
              35,
              null
            ]
          },
          {
            id: "program-r50",
            name: "K\u1EF9 thu\u1EADt x\xE2y d\u1EF1ng",
            code: "5580201",
            sourceRow: 50,
            levels: [
              "Trung c\u1EA5p",
              "S\u01A1 c\u1EA5p"
            ],
            reported: {
              total: 100,
              college: null,
              intermediate: 80,
              elementary: 20
            },
            rawCounts: [
              100,
              null,
              80,
              20
            ]
          },
          {
            id: "program-r51",
            name: "C\u1ED1t th\xE9p - H\xE0n",
            code: "5580208",
            sourceRow: 51,
            levels: [
              "Trung c\u1EA5p",
              "S\u01A1 c\u1EA5p"
            ],
            reported: {
              total: 40,
              college: null,
              intermediate: 30,
              elementary: 10
            },
            rawCounts: [
              40,
              null,
              30,
              10
            ]
          },
          {
            id: "program-r52",
            name: "C\u1EA5p, tho\xE1t n\u01B0\u1EDBc",
            code: "5520312",
            sourceRow: 52,
            levels: [
              "Trung c\u1EA5p",
              "S\u01A1 c\u1EA5p"
            ],
            reported: {
              total: 20,
              college: null,
              intermediate: 10,
              elementary: 10
            },
            rawCounts: [
              20,
              null,
              10,
              10
            ]
          },
          {
            id: "program-r53",
            name: "M\u1ED9c X.d\u1EF1ng v\xE0 trang tr\xED n\u1ED9i th\u1EA5t",
            code: "5580210",
            sourceRow: 53,
            levels: [
              "Trung c\u1EA5p",
              "S\u01A1 c\u1EA5p"
            ],
            reported: {
              total: 20,
              college: null,
              intermediate: 10,
              elementary: 10
            },
            rawCounts: [
              20,
              null,
              10,
              10
            ]
          },
          {
            id: "program-r54",
            name: "M\u1ED9c M\u1EF9 ngh\u1EC7",
            code: "5210421",
            sourceRow: 54,
            levels: [
              "Trung c\u1EA5p",
              "S\u01A1 c\u1EA5p"
            ],
            reported: {
              total: 30,
              college: null,
              intermediate: 10,
              elementary: 20
            },
            rawCounts: [
              30,
              null,
              10,
              20
            ]
          },
          {
            id: "program-r55",
            name: "M\u1ED9c d\xE2n d\u1EE5ng",
            code: "5580211",
            sourceRow: 55,
            levels: [
              "Trung c\u1EA5p"
            ],
            reported: {
              total: 10,
              college: null,
              intermediate: 10,
              elementary: null
            },
            rawCounts: [
              10,
              null,
              10,
              null
            ]
          },
          {
            id: "program-r56",
            name: "K\u1EF9 thu\u1EADt \u0110i\xEAu kh\u1EAFc g\u1ED7",
            code: "5210101",
            sourceRow: 56,
            levels: [
              "Trung c\u1EA5p",
              "S\u01A1 c\u1EA5p"
            ],
            reported: {
              total: 40,
              college: null,
              intermediate: 20,
              elementary: 20
            },
            rawCounts: [
              40,
              null,
              20,
              20
            ]
          },
          {
            id: "program-r57",
            name: "Ch\u1EA1m kh\u1EAFc \u0111\xE1",
            code: "5210408",
            sourceRow: 57,
            levels: [
              "Trung c\u1EA5p"
            ],
            reported: {
              total: 10,
              college: null,
              intermediate: 10,
              elementary: null
            },
            rawCounts: [
              10,
              null,
              10,
              null
            ]
          },
          {
            id: "program-r58",
            name: "K\u1EF9 thu\u1EADt S\u01A1n m\xE0i v\xE0 Kh\u1EA3m trai",
            code: "5210412",
            sourceRow: 58,
            levels: [
              "Trung c\u1EA5p",
              "S\u01A1 c\u1EA5p"
            ],
            reported: {
              total: 20,
              college: null,
              intermediate: 10,
              elementary: 10
            },
            rawCounts: [
              20,
              null,
              10,
              10
            ]
          },
          {
            id: "program-r59",
            name: "H\xE0n",
            code: "5520123",
            sourceRow: 59,
            levels: [
              "Trung c\u1EA5p",
              "S\u01A1 c\u1EA5p"
            ],
            reported: {
              total: 40,
              college: null,
              intermediate: 30,
              elementary: 10
            },
            rawCounts: [
              40,
              null,
              30,
              10
            ]
          },
          {
            id: "program-r60",
            name: "N\u1EC1 ho\xE0n thi\u1EC7n",
            code: "",
            sourceRow: 60,
            levels: [
              "S\u01A1 c\u1EA5p"
            ],
            reported: {
              total: 10,
              college: null,
              intermediate: null,
              elementary: 10
            },
            rawCounts: [
              10,
              null,
              null,
              10
            ]
          }
        ],
        issues: [
          "D\xF2ng 44 l\u1EB7p t\xEAn v\xE0 m\xE3 ngh\u1EC1 \u1EDF d\xF2ng kh\xE1c; gi\u1EEF t\u1EEBng d\xF2ng \u0111\u1EC3 \u0111\u1ED1i chi\u1EBFu tr\xECnh \u0111\u1ED9 v\xE0 s\u1ED1 li\u1EC7u."
        ]
      },
      {
        id: "school-r62",
        name: "Tr\u01B0\u1EDDng C\u0110 C\xF4ng nghi\u1EC7p Nam \u0110\u1ECBnh",
        code: "cdt2501",
        type: "Cao \u0111\u1EB3ng",
        sourceRow: 62,
        reported: {
          total: 0,
          college: 270,
          intermediate: 1e3,
          elementary: 50
        },
        rawCounts: [
          0,
          270,
          1e3,
          50
        ],
        groups: [],
        programs: [
          {
            id: "program-r63",
            name: "C\u1EAFt g\u1ECDt kim lo\u1EA1i",
            code: "5520121",
            sourceRow: 63,
            levels: [],
            reported: {
              total: null,
              college: null,
              intermediate: null,
              elementary: null
            },
            rawCounts: [
              null,
              null,
              null,
              null
            ]
          },
          {
            id: "program-r64",
            name: "C\xF4ng ngh\u1EC7 k\u1EF9 thu\u1EADt nhi\u1EC7t",
            code: "5510211",
            sourceRow: 64,
            levels: [],
            reported: {
              total: null,
              college: null,
              intermediate: null,
              elementary: null
            },
            rawCounts: [
              null,
              null,
              null,
              null
            ]
          },
          {
            id: "program-r65",
            name: "C\xF4ng ngh\u1EC7 may",
            code: "5540204",
            sourceRow: 65,
            levels: [],
            reported: {
              total: null,
              college: null,
              intermediate: null,
              elementary: null
            },
            rawCounts: [
              null,
              null,
              null,
              null
            ]
          },
          {
            id: "program-r66",
            name: "C\xF4ng ngh\u1EC7 \xF4 t\xF4",
            code: "5510216",
            sourceRow: 66,
            levels: [],
            reported: {
              total: null,
              college: null,
              intermediate: null,
              elementary: null
            },
            rawCounts: [
              null,
              null,
              null,
              null
            ]
          },
          {
            id: "program-r67",
            name: "\u0110i\u1EC7n c\xF4ng nghi\u1EC7p",
            code: "5520227",
            sourceRow: 67,
            levels: [],
            reported: {
              total: null,
              college: null,
              intermediate: null,
              elementary: null
            },
            rawCounts: [
              null,
              null,
              null,
              null
            ]
          },
          {
            id: "program-r68",
            name: "\u0110i\u1EC7n t\u1EED c\xF4ng nghi\u1EC7p",
            code: "5520225",
            sourceRow: 68,
            levels: [],
            reported: {
              total: null,
              college: null,
              intermediate: null,
              elementary: null
            },
            rawCounts: [
              null,
              null,
              null,
              null
            ]
          },
          {
            id: "program-r69",
            name: "K\u1EBF to\xE1n doanh nghi\u1EC7p",
            code: "5340302",
            sourceRow: 69,
            levels: [],
            reported: {
              total: null,
              college: null,
              intermediate: null,
              elementary: null
            },
            rawCounts: [
              null,
              null,
              null,
              null
            ]
          },
          {
            id: "program-r70",
            name: "May th\u1EDDi trang",
            code: "5540205",
            sourceRow: 70,
            levels: [],
            reported: {
              total: null,
              college: null,
              intermediate: null,
              elementary: null
            },
            rawCounts: [
              null,
              null,
              null,
              null
            ]
          },
          {
            id: "program-r71",
            name: "S\u1EEDa ch\u1EEFa thi\u1EBFt b\u1ECB may",
            code: "5520133",
            sourceRow: 71,
            levels: [],
            reported: {
              total: null,
              college: null,
              intermediate: null,
              elementary: null
            },
            rawCounts: [
              null,
              null,
              null,
              null
            ]
          },
          {
            id: "program-r72",
            name: "Tin h\u1ECDc v\u0103n ph\xF2ng",
            code: "5480203",
            sourceRow: 72,
            levels: [],
            reported: {
              total: null,
              college: null,
              intermediate: null,
              elementary: null
            },
            rawCounts: [
              null,
              null,
              null,
              null
            ]
          },
          {
            id: "program-r73",
            name: "H\xE0n",
            code: "5520123",
            sourceRow: 73,
            levels: [],
            reported: {
              total: null,
              college: null,
              intermediate: null,
              elementary: null
            },
            rawCounts: [
              null,
              null,
              null,
              null
            ]
          },
          {
            id: "program-r74",
            name: "C\u1EAFt g\u1ECDt kim lo\u1EA1i",
            code: "6520121",
            sourceRow: 74,
            levels: [],
            reported: {
              total: null,
              college: null,
              intermediate: null,
              elementary: null
            },
            rawCounts: [
              null,
              null,
              null,
              null
            ]
          },
          {
            id: "program-r75",
            name: "C\xF4ng ngh\u1EC7 k\u1EF9 thu\u1EADt \u0111i\u1EC1u khi\u1EC3n v\xE0 T\u0110H",
            code: "6510305",
            sourceRow: 75,
            levels: [],
            reported: {
              total: null,
              college: null,
              intermediate: null,
              elementary: null
            },
            rawCounts: [
              null,
              null,
              null,
              null
            ]
          },
          {
            id: "program-r76",
            name: "C\xF4ng ngh\u1EC7 \xF4 t\xF4",
            code: "6510216",
            sourceRow: 76,
            levels: [],
            reported: {
              total: null,
              college: null,
              intermediate: null,
              elementary: null
            },
            rawCounts: [
              null,
              null,
              null,
              null
            ]
          },
          {
            id: "program-r77",
            name: "\u0110i\u1EC7n c\xF4ng nghi\u1EC7p",
            code: "6520227",
            sourceRow: 77,
            levels: [],
            reported: {
              total: null,
              college: null,
              intermediate: null,
              elementary: null
            },
            rawCounts: [
              null,
              null,
              null,
              null
            ]
          },
          {
            id: "program-r78",
            name: "K\u1EBF to\xE1n",
            code: "6340301",
            sourceRow: 78,
            levels: [],
            reported: {
              total: null,
              college: null,
              intermediate: null,
              elementary: null
            },
            rawCounts: [
              null,
              null,
              null,
              null
            ]
          },
          {
            id: "program-r79",
            name: "May th\u1EDDi trang",
            code: "6540205",
            sourceRow: 79,
            levels: [],
            reported: {
              total: null,
              college: null,
              intermediate: null,
              elementary: null
            },
            rawCounts: [
              null,
              null,
              null,
              null
            ]
          }
        ],
        issues: [
          "C\u1ED9t T\u1ED5ng c\u1EE7a c\u01A1 s\u1EDF ch\u01B0a kh\u1EDBp t\u1ED5ng C\u0110, TC, SC trong d\xF2ng ngu\u1ED3n."
        ]
      },
      {
        id: "school-r80",
        name: "Tr\u01B0\u1EDDng C\u0110 Kinh t\u1EBF v\xE0 C\xF4ng ngh\u1EC7 N\u0110",
        code: "CDD2501",
        type: "Cao \u0111\u1EB3ng",
        sourceRow: 80,
        reported: {
          total: 2455,
          college: 300,
          intermediate: 1505,
          elementary: 650
        },
        rawCounts: [
          2455,
          300,
          1505,
          650
        ],
        groups: [],
        programs: [
          {
            id: "program-r81",
            name: "H\xE0n",
            code: "5520123",
            sourceRow: 81,
            levels: [
              "Trung c\u1EA5p"
            ],
            reported: {
              total: null,
              college: null,
              intermediate: 35,
              elementary: null
            },
            rawCounts: [
              null,
              null,
              35,
              null
            ]
          },
          {
            id: "program-r82",
            name: "C\u1EAFt g\u1ECDt kim lo\u1EA1i",
            code: "5520121",
            sourceRow: 82,
            levels: [
              "Trung c\u1EA5p"
            ],
            reported: {
              total: null,
              college: null,
              intermediate: 35,
              elementary: null
            },
            rawCounts: [
              null,
              null,
              35,
              null
            ]
          },
          {
            id: "program-r83",
            name: "C\xF4ng ngh\u1EC7 \xF4 t\xF4",
            code: "5510216",
            sourceRow: 83,
            levels: [
              "Trung c\u1EA5p"
            ],
            reported: {
              total: null,
              college: null,
              intermediate: 150,
              elementary: null
            },
            rawCounts: [
              null,
              null,
              150,
              null
            ]
          },
          {
            id: "program-r84",
            name: "S\u1EEDa ch\u1EEFa thi\u1EBFt b\u1ECB may",
            code: "5520113",
            sourceRow: 84,
            levels: [
              "Trung c\u1EA5p"
            ],
            reported: {
              total: null,
              college: null,
              intermediate: 30,
              elementary: null
            },
            rawCounts: [
              null,
              null,
              30,
              null
            ]
          },
          {
            id: "program-r85",
            name: "\u0110i\u1EC7n c\xF4ng nghi\u1EC7p",
            code: "5520227",
            sourceRow: 85,
            levels: [
              "Trung c\u1EA5p"
            ],
            reported: {
              total: null,
              college: null,
              intermediate: 210,
              elementary: null
            },
            rawCounts: [
              null,
              null,
              210,
              null
            ]
          },
          {
            id: "program-r86",
            name: "\u0110i\u1EC7n t\u1EED c\xF4ng nghi\u1EC7p",
            code: "5520225",
            sourceRow: 86,
            levels: [
              "Trung c\u1EA5p"
            ],
            reported: {
              total: null,
              college: null,
              intermediate: 20,
              elementary: null
            },
            rawCounts: [
              null,
              null,
              20,
              null
            ]
          },
          {
            id: "program-r87",
            name: "\u0110i\u1EC7n c\xF4ng nghi\u1EC7p v\xE0 d\xE2n d\u1EE5ng",
            code: "5520223",
            sourceRow: 87,
            levels: [
              "Trung c\u1EA5p",
              "S\u01A1 c\u1EA5p"
            ],
            reported: {
              total: null,
              college: null,
              intermediate: 45,
              elementary: 105
            },
            rawCounts: [
              null,
              null,
              45,
              105
            ]
          },
          {
            id: "program-r88",
            name: "C\xF4ng ngh\u1EC7 k\u1EF9 thu\u1EADt \u0111i\u1EC7n, \u0111i\u1EC7n t\u1EED",
            code: "5510303",
            sourceRow: 88,
            levels: [
              "Trung c\u1EA5p"
            ],
            reported: {
              total: null,
              college: null,
              intermediate: 45,
              elementary: null
            },
            rawCounts: [
              null,
              null,
              45,
              null
            ]
          },
          {
            id: "program-r89",
            name: "K\u1EF9 thu\u1EADt m\xE1y l\u1EA1nh v\xE0 \u0111i\u1EC1u h\xF2a kh\xF4ng kh\xED",
            code: "5520205",
            sourceRow: 89,
            levels: [
              "Trung c\u1EA5p"
            ],
            reported: {
              total: null,
              college: null,
              intermediate: 70,
              elementary: null
            },
            rawCounts: [
              null,
              null,
              70,
              null
            ]
          },
          {
            id: "program-r90",
            name: "C\xF4ng ngh\u1EC7 th\xF4ng tin",
            code: "5480202",
            sourceRow: 90,
            levels: [
              "Trung c\u1EA5p"
            ],
            reported: {
              total: null,
              college: null,
              intermediate: 60,
              elementary: null
            },
            rawCounts: [
              null,
              null,
              60,
              null
            ]
          },
          {
            id: "program-r91",
            name: "K\u1EF9 thu\u1EADt s\u1EEDa ch\u1EEFa, l\u1EAFp r\xE1p m\xE1y t\xEDnh",
            code: "5480102",
            sourceRow: 91,
            levels: [
              "Trung c\u1EA5p"
            ],
            reported: {
              total: null,
              college: null,
              intermediate: 35,
              elementary: null
            },
            rawCounts: [
              null,
              null,
              35,
              null
            ]
          },
          {
            id: "program-r92",
            name: "May th\u1EDDi trang",
            code: "5540205",
            sourceRow: 92,
            levels: [
              "Trung c\u1EA5p"
            ],
            reported: {
              total: null,
              college: null,
              intermediate: 150,
              elementary: null
            },
            rawCounts: [
              null,
              null,
              150,
              null
            ]
          },
          {
            id: "program-r93",
            name: "Ch\u0103m s\xF3c s\u1EAFc \u0111\u1EB9p",
            code: "5810404",
            sourceRow: 93,
            levels: [
              "Trung c\u1EA5p",
              "S\u01A1 c\u1EA5p"
            ],
            reported: {
              total: null,
              college: null,
              intermediate: 60,
              elementary: 35
            },
            rawCounts: [
              null,
              null,
              60,
              35
            ]
          },
          {
            id: "program-r94",
            name: "K\u1EF9 thu\u1EADt ch\u1EBF bi\u1EBFn m\xF3n \u0103n",
            code: "5810207",
            sourceRow: 94,
            levels: [
              "Trung c\u1EA5p",
              "S\u01A1 c\u1EA5p"
            ],
            reported: {
              total: null,
              college: null,
              intermediate: 90,
              elementary: 150
            },
            rawCounts: [
              null,
              null,
              90,
              150
            ]
          },
          {
            id: "program-r95",
            name: "K\u1EBF to\xE1n doanh nghi\u1EC7p",
            code: "5340302",
            sourceRow: 95,
            levels: [
              "Trung c\u1EA5p"
            ],
            reported: {
              total: null,
              college: null,
              intermediate: 60,
              elementary: null
            },
            rawCounts: [
              null,
              null,
              60,
              null
            ]
          },
          {
            id: "program-r96",
            name: "C\xF4ng ngh\u1EC7 k\u1EF9 thu\u1EADt ch\u1EBF bi\u1EBFn v\xE0 b\u1EA3o qu\u1EA3n n\xF4ng s\u1EA3n",
            code: "5620102",
            sourceRow: 96,
            levels: [
              "Trung c\u1EA5p"
            ],
            reported: {
              total: null,
              college: null,
              intermediate: 90,
              elementary: null
            },
            rawCounts: [
              null,
              null,
              90,
              null
            ]
          },
          {
            id: "program-r97",
            name: "Ch\u0103n nu\xF4i - Th\xFA y",
            code: "5620120",
            sourceRow: 97,
            levels: [
              "Trung c\u1EA5p"
            ],
            reported: {
              total: null,
              college: null,
              intermediate: 25,
              elementary: null
            },
            rawCounts: [
              null,
              null,
              25,
              null
            ]
          },
          {
            id: "program-r98",
            name: "H\xE0n",
            code: "6520123",
            sourceRow: 98,
            levels: [
              "Cao \u0111\u1EB3ng",
              "S\u01A1 c\u1EA5p"
            ],
            reported: {
              total: null,
              college: 30,
              intermediate: null,
              elementary: 35
            },
            rawCounts: [
              null,
              30,
              null,
              35
            ]
          },
          {
            id: "program-r99",
            name: "C\u1EAFt g\u1ECDt kim lo\u1EA1i",
            code: "6520121",
            sourceRow: 99,
            levels: [
              "Cao \u0111\u1EB3ng",
              "S\u01A1 c\u1EA5p"
            ],
            reported: {
              total: null,
              college: 20,
              intermediate: null,
              elementary: 35
            },
            rawCounts: [
              null,
              20,
              null,
              35
            ]
          },
          {
            id: "program-r100",
            name: "C\xF4ng ngh\u1EC7 \xF4 t\xF4",
            code: "6520216",
            sourceRow: 100,
            levels: [
              "Cao \u0111\u1EB3ng"
            ],
            reported: {
              total: null,
              college: 30,
              intermediate: null,
              elementary: null
            },
            rawCounts: [
              null,
              30,
              null,
              null
            ]
          },
          {
            id: "program-r101",
            name: "\u0110i\u1EC7n c\xF4ng nghi\u1EC7p",
            code: "6520227",
            sourceRow: 101,
            levels: [
              "Cao \u0111\u1EB3ng"
            ],
            reported: {
              total: null,
              college: 30,
              intermediate: null,
              elementary: null
            },
            rawCounts: [
              null,
              30,
              null,
              null
            ]
          },
          {
            id: "program-r102",
            name: "K\u1EF9 thu\u1EADt m\xE1y l\u1EA1nh v\xE0 \u0111i\u1EC1u h\xF2a kh\xF4ng kh\xED",
            code: "6520205",
            sourceRow: 102,
            levels: [
              "Cao \u0111\u1EB3ng"
            ],
            reported: {
              total: null,
              college: 30,
              intermediate: null,
              elementary: null
            },
            rawCounts: [
              null,
              30,
              null,
              null
            ]
          },
          {
            id: "program-r103",
            name: "C\xF4ng ngh\u1EC7 th\xF4ng tin",
            code: "6480201",
            sourceRow: 103,
            levels: [
              "Cao \u0111\u1EB3ng"
            ],
            reported: {
              total: null,
              college: 30,
              intermediate: null,
              elementary: null
            },
            rawCounts: [
              null,
              30,
              null,
              null
            ]
          },
          {
            id: "program-r104",
            name: "K\u1EBF to\xE1n",
            code: "6430301",
            sourceRow: 104,
            levels: [
              "Cao \u0111\u1EB3ng"
            ],
            reported: {
              total: null,
              college: 30,
              intermediate: null,
              elementary: null
            },
            rawCounts: [
              null,
              30,
              null,
              null
            ]
          },
          {
            id: "program-r105",
            name: "May th\u1EDDi trang",
            code: "6540205",
            sourceRow: 105,
            levels: [
              "Cao \u0111\u1EB3ng"
            ],
            reported: {
              total: null,
              college: 30,
              intermediate: null,
              elementary: null
            },
            rawCounts: [
              null,
              30,
              null,
              null
            ]
          },
          {
            id: "program-r106",
            name: "K\u1EF9 thu\u1EADt s\u1EEDa ch\u1EEFa, l\u1EAFp r\xE1p m\xE1y t\xEDnh",
            code: "6480102.5480102",
            sourceRow: 106,
            levels: [
              "Cao \u0111\u1EB3ng",
              "Trung c\u1EA5p"
            ],
            reported: {
              total: null,
              college: 30,
              intermediate: 35,
              elementary: null
            },
            rawCounts: [
              null,
              30,
              35,
              null
            ]
          },
          {
            id: "program-r107",
            name: "Qu\u1EA3n l\xFD khai th\xE1c c\xF4ng tr\xECnh thu\u1EF7 l\u1EE3i",
            code: "6340429, 53404229",
            sourceRow: 107,
            levels: [
              "Cao \u0111\u1EB3ng",
              "Trung c\u1EA5p",
              "S\u01A1 c\u1EA5p"
            ],
            reported: {
              total: null,
              college: 20,
              intermediate: 25,
              elementary: 45
            },
            rawCounts: [
              null,
              20,
              25,
              45
            ]
          },
          {
            id: "program-r108",
            name: "V\u1EADn h\xE0nh s\u1EEDa ch\u1EEFa tr\u1EA1m b\u01A1m \u0111i\u1EC7n",
            code: "6520249, 5520259",
            sourceRow: 108,
            levels: [
              "Cao \u0111\u1EB3ng",
              "Trung c\u1EA5p",
              "S\u01A1 c\u1EA5p"
            ],
            reported: {
              total: null,
              college: 20,
              intermediate: 25,
              elementary: 70
            },
            rawCounts: [
              null,
              20,
              25,
              70
            ]
          },
          {
            id: "program-r109",
            name: "Nghi\u1EC7p v\u1EE5 l\u1EC5 t\xE2n",
            code: "5810203",
            sourceRow: 109,
            levels: [
              "Trung c\u1EA5p"
            ],
            reported: {
              total: null,
              college: null,
              intermediate: 25,
              elementary: null
            },
            rawCounts: [
              null,
              null,
              25,
              null
            ]
          },
          {
            id: "program-r110",
            name: "Nghi\u1EC7p v\u1EE5 nh\xE0 h\xE0ng",
            code: "5810206",
            sourceRow: 110,
            levels: [
              "Trung c\u1EA5p"
            ],
            reported: {
              total: null,
              college: null,
              intermediate: 25,
              elementary: null
            },
            rawCounts: [
              null,
              null,
              25,
              null
            ]
          },
          {
            id: "program-r111",
            name: "Qu\u1EA3n tr\u1ECB kh\xE1ch s\u1EA1n",
            code: "5810201",
            sourceRow: 111,
            levels: [
              "Trung c\u1EA5p"
            ],
            reported: {
              total: null,
              college: null,
              intermediate: 25,
              elementary: null
            },
            rawCounts: [
              null,
              null,
              25,
              null
            ]
          },
          {
            id: "program-r112",
            name: "Qu\u1EA3n l\xFD \u0111\u1EA5t \u0111ai",
            code: "5850102",
            sourceRow: 112,
            levels: [
              "Trung c\u1EA5p",
              "S\u01A1 c\u1EA5p"
            ],
            reported: {
              total: null,
              college: null,
              intermediate: 20,
              elementary: 70
            },
            rawCounts: [
              null,
              null,
              20,
              70
            ]
          },
          {
            id: "program-r113",
            name: "K\u1EF9 thu\u1EADt \u0111i\xEAu kh\u1EAFc g\u1ED7",
            code: "5210101",
            sourceRow: 113,
            levels: [
              "Trung c\u1EA5p",
              "S\u01A1 c\u1EA5p"
            ],
            reported: {
              total: null,
              college: null,
              intermediate: 20,
              elementary: 35
            },
            rawCounts: [
              null,
              null,
              20,
              35
            ]
          },
          {
            id: "program-r114",
            name: "Nu\xF4i tr\u1ED3ng thu\u1EF7 s\u1EA3n",
            code: "5620303",
            sourceRow: 114,
            levels: [
              "Trung c\u1EA5p"
            ],
            reported: {
              total: null,
              college: null,
              intermediate: 25,
              elementary: null
            },
            rawCounts: [
              null,
              null,
              25,
              null
            ]
          },
          {
            id: "program-r115",
            name: "Tr\u1ED3ng tr\u1ECDt v\xE0 b\u1EA3o v\u1EC7 th\u1EF1c v\u1EADt",
            code: "5620111",
            sourceRow: 115,
            levels: [
              "Trung c\u1EA5p"
            ],
            reported: {
              total: null,
              college: null,
              intermediate: 20,
              elementary: null
            },
            rawCounts: [
              null,
              null,
              20,
              null
            ]
          },
          {
            id: "program-r116",
            name: "Ch\u0103n nu\xF4i - Th\xFA y",
            code: "5620120",
            sourceRow: 116,
            levels: [
              "Trung c\u1EA5p",
              "S\u01A1 c\u1EA5p"
            ],
            reported: {
              total: null,
              college: null,
              intermediate: 25,
              elementary: 70
            },
            rawCounts: [
              null,
              null,
              25,
              70
            ]
          },
          {
            id: "program-r117",
            name: "K\u1EBF to\xE1n h\xE0nh ch\xEDnh s\u1EF1 nghi\u1EC7p",
            code: "5340307",
            sourceRow: 117,
            levels: [
              "Trung c\u1EA5p"
            ],
            reported: {
              total: null,
              college: null,
              intermediate: 25,
              elementary: null
            },
            rawCounts: [
              null,
              null,
              25,
              null
            ]
          }
        ],
        issues: [
          "D\xF2ng 116 l\u1EB7p t\xEAn v\xE0 m\xE3 ngh\u1EC1 \u1EDF d\xF2ng kh\xE1c; gi\u1EEF t\u1EEBng d\xF2ng \u0111\u1EC3 \u0111\u1ED1i chi\u1EBFu tr\xECnh \u0111\u1ED9 v\xE0 s\u1ED1 li\u1EC7u."
        ]
      },
      {
        id: "school-r118",
        name: "Tr\u01B0\u1EDDng Cao \u0111\u1EB3ng VHNT & Du l\u1ECBch N\u0110",
        code: "cdd2502",
        type: "Cao \u0111\u1EB3ng",
        sourceRow: 118,
        reported: {
          total: 205,
          college: 60,
          intermediate: 145,
          elementary: 0
        },
        rawCounts: [
          205,
          60,
          145,
          0
        ],
        groups: [],
        programs: [
          {
            id: "program-r119",
            name: "Thanh nh\u1EA1c",
            code: "6210225",
            sourceRow: 119,
            levels: [
              "Cao \u0111\u1EB3ng"
            ],
            reported: {
              total: 15,
              college: 15,
              intermediate: null,
              elementary: null
            },
            rawCounts: [
              15,
              15,
              null,
              null
            ]
          },
          {
            id: "program-r120",
            name: "H\u01B0\u1EDBng d\u1EABn du l\u1ECBch",
            code: "6810103",
            sourceRow: 120,
            levels: [
              "Cao \u0111\u1EB3ng"
            ],
            reported: {
              total: 10,
              college: 10,
              intermediate: null,
              elementary: null
            },
            rawCounts: [
              10,
              10,
              null,
              null
            ]
          },
          {
            id: "program-r121",
            name: "Khoa h\u1ECDc th\u01B0 vi\u1EC7n",
            code: "6320206",
            sourceRow: 121,
            levels: [
              "Cao \u0111\u1EB3ng"
            ],
            reported: {
              total: 10,
              college: 10,
              intermediate: null,
              elementary: null
            },
            rawCounts: [
              10,
              10,
              null,
              null
            ]
          },
          {
            id: "program-r122",
            name: "Thi\u1EBFt k\u1EBF \u0111\u1ED3 h\u1ECDa",
            code: "6210402",
            sourceRow: 122,
            levels: [
              "Cao \u0111\u1EB3ng"
            ],
            reported: {
              total: 25,
              college: 25,
              intermediate: null,
              elementary: null
            },
            rawCounts: [
              25,
              25,
              null,
              null
            ]
          },
          {
            id: "program-r123",
            name: "Thanh nh\u1EA1c",
            code: "5210225",
            sourceRow: 123,
            levels: [
              "Trung c\u1EA5p"
            ],
            reported: {
              total: 15,
              college: null,
              intermediate: 15,
              elementary: null
            },
            rawCounts: [
              15,
              null,
              15,
              null
            ]
          },
          {
            id: "program-r124",
            name: "Organ",
            code: "5210224",
            sourceRow: 124,
            levels: [
              "Trung c\u1EA5p"
            ],
            reported: {
              total: 10,
              college: null,
              intermediate: 10,
              elementary: null
            },
            rawCounts: [
              10,
              null,
              10,
              null
            ]
          },
          {
            id: "program-r125",
            name: "Bi\u1EC3u di\u1EC5n nh\u1EA1c c\u1EE5 truy\u1EC1n th\u1ED1ng",
            code: "5210216",
            sourceRow: 125,
            levels: [
              "Trung c\u1EA5p"
            ],
            reported: {
              total: 5,
              college: null,
              intermediate: 5,
              elementary: null
            },
            rawCounts: [
              5,
              null,
              5,
              null
            ]
          },
          {
            id: "program-r126",
            name: "H\u1ED9i h\u1ECDa",
            code: "5210103",
            sourceRow: 126,
            levels: [
              "Trung c\u1EA5p"
            ],
            reported: {
              total: 15,
              college: null,
              intermediate: 15,
              elementary: null
            },
            rawCounts: [
              15,
              null,
              15,
              null
            ]
          },
          {
            id: "program-r127",
            name: "Thi\u1EBFt k\u1EBF \u0111\u1ED3 h\u1ECDa",
            code: "5210402",
            sourceRow: 127,
            levels: [
              "Trung c\u1EA5p"
            ],
            reported: {
              total: 20,
              college: null,
              intermediate: 20,
              elementary: null
            },
            rawCounts: [
              20,
              null,
              20,
              null
            ]
          },
          {
            id: "program-r128",
            name: "Ngh\u1EC7 thu\u1EADt bi\u1EC3u di\u1EC5n Ch\xE8o",
            code: "5210203",
            sourceRow: 128,
            levels: [
              "Trung c\u1EA5p"
            ],
            reported: {
              total: 15,
              college: null,
              intermediate: 15,
              elementary: null
            },
            rawCounts: [
              15,
              null,
              15,
              null
            ]
          },
          {
            id: "program-r129",
            name: "Ngh\u1EC7 thu\u1EADt bi\u1EC3u di\u1EC5n K\u1ECBch n\xF3i",
            code: "5210210",
            sourceRow: 129,
            levels: [
              "Trung c\u1EA5p"
            ],
            reported: {
              total: 10,
              college: null,
              intermediate: 10,
              elementary: null
            },
            rawCounts: [
              10,
              null,
              10,
              null
            ]
          },
          {
            id: "program-r130",
            name: "H\u01B0\u1EDBng d\u1EABn du l\u1ECBch",
            code: "5810103",
            sourceRow: 130,
            levels: [
              "Trung c\u1EA5p"
            ],
            reported: {
              total: 45,
              college: null,
              intermediate: 45,
              elementary: null
            },
            rawCounts: [
              45,
              null,
              45,
              null
            ]
          },
          {
            id: "program-r131",
            name: "Th\u01B0 vi\u1EC7n",
            code: "5320205",
            sourceRow: 131,
            levels: [
              "Trung c\u1EA5p"
            ],
            reported: {
              total: 10,
              college: null,
              intermediate: 10,
              elementary: null
            },
            rawCounts: [
              10,
              null,
              10,
              null
            ]
          }
        ],
        issues: []
      },
      {
        id: "school-r132",
        name: "Tr\u01B0\u1EDDng Cao \u0111\u1EB3ng C\u01A1 gi\u1EDBi Ninh B\xECnh",
        code: "CDT2702",
        type: "Cao \u0111\u1EB3ng",
        sourceRow: 132,
        reported: {
          total: 2620,
          college: 220,
          intermediate: 900,
          elementary: 1500
        },
        rawCounts: [
          2620,
          220,
          900,
          1500
        ],
        groups: [],
        programs: [
          {
            id: "program-r133",
            name: "C\xF4ng ngh\u1EC7 \xF4t\xF4",
            code: "6510216",
            sourceRow: 133,
            levels: [
              "Cao \u0111\u1EB3ng",
              "Trung c\u1EA5p",
              "S\u01A1 c\u1EA5p"
            ],
            reported: {
              total: 350,
              college: 55,
              intermediate: 245,
              elementary: 50
            },
            rawCounts: [
              350,
              55,
              245,
              50
            ]
          },
          {
            id: "program-r134",
            name: "H\xE0n",
            code: "6520123",
            sourceRow: 134,
            levels: [
              "Cao \u0111\u1EB3ng",
              "Trung c\u1EA5p",
              "S\u01A1 c\u1EA5p"
            ],
            reported: {
              total: 55,
              college: 10,
              intermediate: 30,
              elementary: 15
            },
            rawCounts: [
              55,
              10,
              30,
              15
            ]
          },
          {
            id: "program-r135",
            name: "C\u1EAFt g\u1ECDt kim lo\u1EA1i",
            code: "6520121",
            sourceRow: 135,
            levels: [
              "Cao \u0111\u1EB3ng",
              "Trung c\u1EA5p",
              "S\u01A1 c\u1EA5p"
            ],
            reported: {
              total: 40,
              college: 10,
              intermediate: 20,
              elementary: 10
            },
            rawCounts: [
              40,
              10,
              20,
              10
            ]
          },
          {
            id: "program-r136",
            name: "\u0110i\u1EC7n c\xF4ng nghi\u1EC7p",
            code: "6520227",
            sourceRow: 136,
            levels: [
              "Cao \u0111\u1EB3ng",
              "Trung c\u1EA5p",
              "S\u01A1 c\u1EA5p"
            ],
            reported: {
              total: 110,
              college: 50,
              intermediate: 50,
              elementary: 10
            },
            rawCounts: [
              110,
              50,
              50,
              10
            ]
          },
          {
            id: "program-r137",
            name: "K\u1EF9 thu\u1EADt m\xE1y l\u1EA1nh v\xE0 \u0111i\u1EC1u ho\xE0 kh\xF4ng kh\xED",
            code: "6520205",
            sourceRow: 137,
            levels: [
              "Cao \u0111\u1EB3ng",
              "Trung c\u1EA5p",
              "S\u01A1 c\u1EA5p"
            ],
            reported: {
              total: 195,
              college: 35,
              intermediate: 150,
              elementary: 10
            },
            rawCounts: [
              195,
              35,
              150,
              10
            ]
          },
          {
            id: "program-r138",
            name: "L\u1EADp tr\xECnh m\xE1y t\xEDnh",
            code: "6480207",
            sourceRow: 138,
            levels: [
              "Cao \u0111\u1EB3ng",
              "Trung c\u1EA5p"
            ],
            reported: {
              total: 15,
              college: 5,
              intermediate: 10,
              elementary: null
            },
            rawCounts: [
              15,
              5,
              10,
              null
            ]
          },
          {
            id: "program-r139",
            name: "K\u1EBF to\xE1n doanh nghi\u1EC7p",
            code: "6340302",
            sourceRow: 139,
            levels: [
              "Cao \u0111\u1EB3ng",
              "Trung c\u1EA5p"
            ],
            reported: {
              total: 25,
              college: 10,
              intermediate: 15,
              elementary: null
            },
            rawCounts: [
              25,
              10,
              15,
              null
            ]
          },
          {
            id: "program-r140",
            name: "K\u1EF9 thu\u1EADt ch\u1EBF bi\u1EBFn m\xF3n \u0103n",
            code: "6810207",
            sourceRow: 140,
            levels: [
              "Cao \u0111\u1EB3ng",
              "Trung c\u1EA5p",
              "S\u01A1 c\u1EA5p"
            ],
            reported: {
              total: 295,
              college: 35,
              intermediate: 230,
              elementary: 30
            },
            rawCounts: [
              295,
              35,
              230,
              30
            ]
          },
          {
            id: "program-r141",
            name: "V\u1EADn h\xE0nh m\xE1y thi c\xF4ng n\u1EC1n",
            code: "5520183",
            sourceRow: 141,
            levels: [
              "Trung c\u1EA5p",
              "S\u01A1 c\u1EA5p"
            ],
            reported: {
              total: 80,
              college: null,
              intermediate: 60,
              elementary: 20
            },
            rawCounts: [
              80,
              null,
              60,
              20
            ]
          },
          {
            id: "program-r142",
            name: "V\u1EADn h\xE0nh C\u1EA7n, c\u1EA7u tr\u1EE5c",
            code: "5520182",
            sourceRow: 142,
            levels: [
              "Trung c\u1EA5p",
              "S\u01A1 c\u1EA5p"
            ],
            reported: {
              total: 50,
              college: null,
              intermediate: 10,
              elementary: 40
            },
            rawCounts: [
              50,
              null,
              10,
              40
            ]
          },
          {
            id: "program-r143",
            name: "\u0110i\u1EC7n d\xE2n d\u1EE5ng",
            code: "6520226",
            sourceRow: 143,
            levels: [
              "Cao \u0111\u1EB3ng",
              "Trung c\u1EA5p",
              "S\u01A1 c\u1EA5p"
            ],
            reported: {
              total: 75,
              college: 5,
              intermediate: 60,
              elementary: 10
            },
            rawCounts: [
              75,
              5,
              60,
              10
            ]
          },
          {
            id: "program-r144",
            name: "VH m\xE1y thi c\xF4ng m\u1EB7t \u0111\u01B0\u1EDDng",
            code: "5520184",
            sourceRow: 144,
            levels: [
              "Trung c\u1EA5p",
              "S\u01A1 c\u1EA5p"
            ],
            reported: {
              total: 15,
              college: null,
              intermediate: 5,
              elementary: 10
            },
            rawCounts: [
              15,
              null,
              5,
              10
            ]
          },
          {
            id: "program-r145",
            name: "S\u1EEDa ch\u1EEFa m\xE1y thi c\xF4ng x\xE2y d\u1EF1ng",
            code: "6520146",
            sourceRow: 145,
            levels: [
              "Cao \u0111\u1EB3ng",
              "Trung c\u1EA5p"
            ],
            reported: {
              total: 10,
              college: 5,
              intermediate: 5,
              elementary: null
            },
            rawCounts: [
              10,
              5,
              5,
              null
            ]
          },
          {
            id: "program-r146",
            name: "C\u1EA5p tho\xE1t n\u01B0\u1EDBc",
            code: "5520312",
            sourceRow: 146,
            levels: [
              "Trung c\u1EA5p"
            ],
            reported: {
              total: 5,
              college: null,
              intermediate: 5,
              elementary: null
            },
            rawCounts: [
              5,
              null,
              5,
              null
            ]
          },
          {
            id: "program-r147",
            name: "B\u1EA3o tr\xEC h\u1EC7 th\u1ED1ng thi\u1EBFt b\u1ECB c\u01A1 kh\xED",
            code: "6520151",
            sourceRow: 147,
            levels: [],
            reported: {
              total: null,
              college: null,
              intermediate: null,
              elementary: null
            },
            rawCounts: [
              null,
              null,
              null,
              null
            ]
          },
          {
            id: "program-r148",
            name: "C\xF4ng t\xE1c x\xE3 h\u1ED9i",
            code: "6760101",
            sourceRow: 148,
            levels: [
              "Trung c\u1EA5p"
            ],
            reported: {
              total: 5,
              college: null,
              intermediate: 5,
              elementary: null
            },
            rawCounts: [
              5,
              null,
              5,
              null
            ]
          },
          {
            id: "program-r149",
            name: "H\xE0n 3G, 5G, 6G",
            code: "",
            sourceRow: 149,
            levels: [
              "S\u01A1 c\u1EA5p"
            ],
            reported: {
              total: 15,
              college: null,
              intermediate: null,
              elementary: 15
            },
            rawCounts: [
              15,
              null,
              null,
              15
            ]
          },
          {
            id: "program-r150",
            name: "V\u1EADn h\xE0nh C\u1EA7u tr\u1EE5c",
            code: "",
            sourceRow: 150,
            levels: [],
            reported: {
              total: null,
              college: null,
              intermediate: null,
              elementary: null
            },
            rawCounts: [
              null,
              null,
              null,
              null
            ]
          },
          {
            id: "program-r151",
            name: "V\u1EADn h\xE0nh m\xE1y x\xFAc l\u1EADt",
            code: "",
            sourceRow: 151,
            levels: [
              "S\u01A1 c\u1EA5p"
            ],
            reported: {
              total: 10,
              college: null,
              intermediate: null,
              elementary: 10
            },
            rawCounts: [
              10,
              null,
              null,
              10
            ]
          },
          {
            id: "program-r152",
            name: "V\u1EADn h\xE0nh m\xE1y x\xFAc",
            code: "",
            sourceRow: 152,
            levels: [
              "S\u01A1 c\u1EA5p"
            ],
            reported: {
              total: 30,
              college: null,
              intermediate: null,
              elementary: 30
            },
            rawCounts: [
              30,
              null,
              null,
              30
            ]
          },
          {
            id: "program-r153",
            name: "V\u1EADn h\xE0nh m\xE1y lu",
            code: "",
            sourceRow: 153,
            levels: [
              "S\u01A1 c\u1EA5p"
            ],
            reported: {
              total: 15,
              college: null,
              intermediate: null,
              elementary: 15
            },
            rawCounts: [
              15,
              null,
              null,
              15
            ]
          },
          {
            id: "program-r154",
            name: "V\u1EADn h\xE0nh xe n\xE2ng h\xE0ng",
            code: "",
            sourceRow: 154,
            levels: [
              "S\u01A1 c\u1EA5p"
            ],
            reported: {
              total: 55,
              college: null,
              intermediate: null,
              elementary: 55
            },
            rawCounts: [
              55,
              null,
              null,
              55
            ]
          },
          {
            id: "program-r155",
            name: "V\u1EADn h\xE0nh C\u1EA7n tr\u1EE5c",
            code: "",
            sourceRow: 155,
            levels: [
              "S\u01A1 c\u1EA5p"
            ],
            reported: {
              total: 30,
              college: null,
              intermediate: null,
              elementary: 30
            },
            rawCounts: [
              30,
              null,
              null,
              30
            ]
          },
          {
            id: "program-r156",
            name: "V\u1EADn h\xE0nh C\u1EA7u tr\u1EE5c",
            code: "",
            sourceRow: 156,
            levels: [
              "S\u01A1 c\u1EA5p"
            ],
            reported: {
              total: 50,
              college: null,
              intermediate: null,
              elementary: 50
            },
            rawCounts: [
              50,
              null,
              null,
              50
            ]
          },
          {
            id: "program-r157",
            name: "V\u1EADn h\xE0nh m\xE1y \u1EE7i",
            code: "",
            sourceRow: 157,
            levels: [
              "S\u01A1 c\u1EA5p"
            ],
            reported: {
              total: 10,
              college: null,
              intermediate: null,
              elementary: 10
            },
            rawCounts: [
              10,
              null,
              null,
              10
            ]
          },
          {
            id: "program-r158",
            name: "\u0110i\u1EC7n n\u01B0\u1EDBc",
            code: "",
            sourceRow: 158,
            levels: [
              "S\u01A1 c\u1EA5p"
            ],
            reported: {
              total: 10,
              college: null,
              intermediate: null,
              elementary: 10
            },
            rawCounts: [
              10,
              null,
              null,
              10
            ]
          },
          {
            id: "program-r159",
            name: "K\u1EF9 thu\u1EADt pha ch\u1EBF \u0111\u1ED3 u\u1ED1ng",
            code: "",
            sourceRow: 159,
            levels: [
              "S\u01A1 c\u1EA5p"
            ],
            reported: {
              total: 5,
              college: null,
              intermediate: null,
              elementary: 5
            },
            rawCounts: [
              5,
              null,
              null,
              5
            ]
          },
          {
            id: "program-r160",
            name: "L\xE1i xe \xF4 t\xF4 h\u1EA1ng B, B.01, C1",
            code: "",
            sourceRow: 160,
            levels: [
              "S\u01A1 c\u1EA5p"
            ],
            reported: {
              total: 900,
              college: null,
              intermediate: null,
              elementary: 900
            },
            rawCounts: [
              900,
              null,
              null,
              900
            ]
          },
          {
            id: "program-r161",
            name: "S\u01B0 ph\u1EA1m d\u1EA1y ngh\u1EC1",
            code: "",
            sourceRow: 161,
            levels: [
              "S\u01A1 c\u1EA5p"
            ],
            reported: {
              total: 150,
              college: null,
              intermediate: null,
              elementary: 150
            },
            rawCounts: [
              150,
              null,
              null,
              150
            ]
          },
          {
            id: "program-r162",
            name: "Th\xFA y",
            code: "",
            sourceRow: 162,
            levels: [
              "S\u01A1 c\u1EA5p"
            ],
            reported: {
              total: 15,
              college: null,
              intermediate: null,
              elementary: 15
            },
            rawCounts: [
              15,
              null,
              null,
              15
            ]
          }
        ],
        issues: [
          "D\xF2ng 156 l\u1EB7p t\xEAn v\xE0 m\xE3 ngh\u1EC1 \u1EDF d\xF2ng kh\xE1c; gi\u1EEF t\u1EEBng d\xF2ng \u0111\u1EC3 \u0111\u1ED1i chi\u1EBFu tr\xECnh \u0111\u1ED9 v\xE0 s\u1ED1 li\u1EC7u."
        ]
      },
      {
        id: "school-r163",
        name: "Tr\u01B0\u1EDDng Cao \u0111\u1EB3ng ngh\u1EC1 LILAMA 1",
        code: "C\u0110T 2703",
        type: "Cao \u0111\u1EB3ng",
        sourceRow: 163,
        reported: {
          total: 1090,
          college: 70,
          intermediate: 420,
          elementary: 600
        },
        rawCounts: [
          1090,
          70,
          420,
          600
        ],
        groups: [],
        programs: [
          {
            id: "program-r164",
            name: "H\xE0n",
            code: "5520123",
            sourceRow: 164,
            levels: [
              "Cao \u0111\u1EB3ng",
              "Trung c\u1EA5p"
            ],
            reported: {
              total: 125,
              college: 25,
              intermediate: 100,
              elementary: null
            },
            rawCounts: [
              125,
              25,
              100,
              null
            ]
          },
          {
            id: "program-r165",
            name: "K\u1EF9 thu\u1EADt l\u1EAFp \u0111\u1EB7t TB\u0110 v\xE0 \u0110K trong CN",
            code: "5520201",
            sourceRow: 165,
            levels: [
              "Cao \u0111\u1EB3ng",
              "Trung c\u1EA5p"
            ],
            reported: {
              total: 170,
              college: 25,
              intermediate: 145,
              elementary: null
            },
            rawCounts: [
              170,
              25,
              145,
              null
            ]
          },
          {
            id: "program-r166",
            name: "C\xF4ng ngh\u1EC7 \xF4 t\xF4",
            code: "5510216",
            sourceRow: 166,
            levels: [
              "Trung c\u1EA5p"
            ],
            reported: {
              total: 30,
              college: null,
              intermediate: 30,
              elementary: null
            },
            rawCounts: [
              30,
              null,
              30,
              null
            ]
          },
          {
            id: "program-r167",
            name: "K\u1EF9 thu\u1EADt m\xE1y l\u1EA1nh v\xE0 \u0110HKK",
            code: "5520205",
            sourceRow: 167,
            levels: [
              "Trung c\u1EA5p"
            ],
            reported: {
              total: 100,
              college: null,
              intermediate: 100,
              elementary: null
            },
            rawCounts: [
              100,
              null,
              100,
              null
            ]
          },
          {
            id: "program-r168",
            name: "Ch\u1EBF t\u1EA1o thi\u1EBFt b\u1ECB c\u01A1 kh\xED",
            code: "5520104",
            sourceRow: 168,
            levels: [
              "Cao \u0111\u1EB3ng",
              "Trung c\u1EA5p"
            ],
            reported: {
              total: 65,
              college: 20,
              intermediate: 45,
              elementary: null
            },
            rawCounts: [
              65,
              20,
              45,
              null
            ]
          },
          {
            id: "program-r169",
            name: "L\xE1i xe \xF4 t\xF4 B, C1",
            code: "",
            sourceRow: 169,
            levels: [
              "S\u01A1 c\u1EA5p"
            ],
            reported: {
              total: 570,
              college: null,
              intermediate: null,
              elementary: 570
            },
            rawCounts: [
              570,
              null,
              null,
              570
            ]
          },
          {
            id: "program-r170",
            name: "B\u1ED3i d\u01B0\u1EE1ng NVSP cho NG d\u1EA1y SC, TC-CD",
            code: "",
            sourceRow: 170,
            levels: [
              "S\u01A1 c\u1EA5p"
            ],
            reported: {
              total: 30,
              college: null,
              intermediate: null,
              elementary: 30
            },
            rawCounts: [
              30,
              null,
              null,
              30
            ]
          }
        ],
        issues: []
      },
      {
        id: "school-r171",
        name: "Tr\u01B0\u1EDDng cao \u0111\u1EB3ng ph\xE1t thanh truy\u1EC1n h\xECnh I",
        code: "CPT",
        type: "Cao \u0111\u1EB3ng",
        sourceRow: 171,
        reported: {
          total: null,
          college: null,
          intermediate: null,
          elementary: null
        },
        rawCounts: [
          null,
          null,
          null,
          null
        ],
        groups: [
          {
            label: "H\u1EC7 cao \u0111\u1EB3ng",
            level: "Cao \u0111\u1EB3ng",
            sourceRow: 172,
            reported: {
              total: 225,
              college: 225,
              intermediate: null,
              elementary: null
            },
            rawCounts: [
              225,
              225,
              null,
              null
            ]
          },
          {
            label: "H\u1EC7 Trung c\u1EA5p",
            level: "Trung c\u1EA5p",
            sourceRow: 180,
            reported: {
              total: 515,
              college: null,
              intermediate: 515,
              elementary: null
            },
            rawCounts: [
              515,
              null,
              515,
              null
            ]
          },
          {
            label: "S\u01A1 c\u1EA5p",
            level: "S\u01A1 c\u1EA5p",
            sourceRow: 186,
            reported: {
              total: 250,
              college: null,
              intermediate: null,
              elementary: 250
            },
            rawCounts: [
              250,
              null,
              null,
              250
            ]
          }
        ],
        programs: [
          {
            id: "program-r173",
            name: "B\xE1o ch\xED",
            code: "6320103",
            sourceRow: 173,
            levels: [
              "Cao \u0111\u1EB3ng"
            ],
            reported: {
              total: 70,
              college: 70,
              intermediate: null,
              elementary: null
            },
            rawCounts: [
              70,
              70,
              null,
              null
            ]
          },
          {
            id: "program-r174",
            name: "Quan h\u1EC7 c\xF4ng ch\xFAng",
            code: "6320108",
            sourceRow: 174,
            levels: [
              "Cao \u0111\u1EB3ng"
            ],
            reported: {
              total: 25,
              college: 25,
              intermediate: null,
              elementary: null
            },
            rawCounts: [
              25,
              25,
              null,
              null
            ]
          },
          {
            id: "program-r175",
            name: "K\u1EBF to\xE1n",
            code: "6340301",
            sourceRow: 175,
            levels: [
              "Cao \u0111\u1EB3ng"
            ],
            reported: {
              total: 25,
              college: 25,
              intermediate: null,
              elementary: null
            },
            rawCounts: [
              25,
              25,
              null,
              null
            ]
          },
          {
            id: "program-r176",
            name: "C\xF4ng ngh\u1EC7 th\xF4ng tin",
            code: "6480201",
            sourceRow: 176,
            levels: [
              "Cao \u0111\u1EB3ng"
            ],
            reported: {
              total: 30,
              college: 30,
              intermediate: null,
              elementary: null
            },
            rawCounts: [
              30,
              30,
              null,
              null
            ]
          },
          {
            id: "program-r177",
            name: "C\xF4ng ngh\u1EC7 KT \u0111i\u1EC7n, \u0111i\u1EC7n t\u1EED",
            code: "6510303",
            sourceRow: 177,
            levels: [
              "Cao \u0111\u1EB3ng"
            ],
            reported: {
              total: 30,
              college: 30,
              intermediate: null,
              elementary: null
            },
            rawCounts: [
              30,
              30,
              null,
              null
            ]
          },
          {
            id: "program-r178",
            name: "C\xF4ng ngh\u1EC7 KT \u0111i\u1EC7n t\u1EED truy\u1EC1n th\xF4ng",
            code: "6510312",
            sourceRow: 178,
            levels: [
              "Cao \u0111\u1EB3ng"
            ],
            reported: {
              total: 25,
              college: 25,
              intermediate: null,
              elementary: null
            },
            rawCounts: [
              25,
              25,
              null,
              null
            ]
          },
          {
            id: "program-r179",
            name: "Ti\u1EBFng Anh",
            code: "6220206",
            sourceRow: 179,
            levels: [
              "Cao \u0111\u1EB3ng"
            ],
            reported: {
              total: 20,
              college: 20,
              intermediate: null,
              elementary: null
            },
            rawCounts: [
              20,
              20,
              null,
              null
            ]
          },
          {
            id: "program-r181",
            name: "B\xE1o ch\xED",
            code: "5320103",
            sourceRow: 181,
            levels: [
              "Trung c\u1EA5p"
            ],
            reported: {
              total: 75,
              college: null,
              intermediate: 75,
              elementary: null
            },
            rawCounts: [
              75,
              null,
              75,
              null
            ]
          },
          {
            id: "program-r182",
            name: "K\u1EBF to\xE1n doanh nghi\u1EC7p",
            code: "5340302",
            sourceRow: 182,
            levels: [
              "Trung c\u1EA5p"
            ],
            reported: {
              total: 75,
              college: null,
              intermediate: 75,
              elementary: null
            },
            rawCounts: [
              75,
              null,
              75,
              null
            ]
          },
          {
            id: "program-r183",
            name: "Tin h\u1ECDc \u1EE9ng d\u1EE5ng",
            code: "5480205",
            sourceRow: 183,
            levels: [
              "Trung c\u1EA5p"
            ],
            reported: {
              total: 190,
              college: null,
              intermediate: 190,
              elementary: null
            },
            rawCounts: [
              190,
              null,
              190,
              null
            ]
          },
          {
            id: "program-r184",
            name: "C\xF4ng ngh\u1EC7 KT \u0111i\u1EC7n, \u0111i\u1EC7n t\u1EED",
            code: "5510303",
            sourceRow: 184,
            levels: [
              "Trung c\u1EA5p"
            ],
            reported: {
              total: 150,
              college: null,
              intermediate: 150,
              elementary: null
            },
            rawCounts: [
              150,
              null,
              150,
              null
            ]
          },
          {
            id: "program-r185",
            name: "C\xF4ng ngh\u1EC7 KT \u0111i\u1EC7n t\u1EED truy\u1EC1n th\xF4ng",
            code: "5510312",
            sourceRow: 185,
            levels: [
              "Trung c\u1EA5p"
            ],
            reported: {
              total: 25,
              college: null,
              intermediate: 25,
              elementary: null
            },
            rawCounts: [
              25,
              null,
              25,
              null
            ]
          }
        ],
        issues: []
      },
      {
        id: "school-r187",
        name: "TR\u01AF\u1EDCNG CAO \u0110\u1EB2NG Y T\u1EBE NINH B\xCCNH",
        code: "CDD2701 (CNY)",
        type: "Cao \u0111\u1EB3ng",
        sourceRow: 187,
        reported: {
          total: 300,
          college: 280,
          intermediate: 20,
          elementary: null
        },
        rawCounts: [
          300,
          280,
          20,
          null
        ],
        groups: [],
        programs: [
          {
            id: "program-r188",
            name: "Cao \u0111\u1EB3ng \u0110i\u1EC1u d\u01B0\u1EE1ng",
            code: "6720301",
            sourceRow: 188,
            levels: [
              "Cao \u0111\u1EB3ng"
            ],
            reported: {
              total: 140,
              college: 140,
              intermediate: null,
              elementary: null
            },
            rawCounts: [
              140,
              140,
              "-",
              "-"
            ]
          },
          {
            id: "program-r189",
            name: "Cao \u0111\u1EB3ng D\u01B0\u1EE3c",
            code: "6720201",
            sourceRow: 189,
            levels: [
              "Cao \u0111\u1EB3ng"
            ],
            reported: {
              total: 140,
              college: 140,
              intermediate: null,
              elementary: null
            },
            rawCounts: [
              140,
              140,
              "-",
              "-"
            ]
          },
          {
            id: "program-r190",
            name: "Y s\u1EF9 \u0111a khoa (TC)",
            code: "5720101",
            sourceRow: 190,
            levels: [
              "Trung c\u1EA5p"
            ],
            reported: {
              total: 20,
              college: null,
              intermediate: 20,
              elementary: null
            },
            rawCounts: [
              20,
              "-",
              20,
              "-"
            ]
          }
        ],
        issues: []
      },
      {
        id: "school-r191",
        name: "Tr\u01B0\u1EDDng Cao \u0111\u1EB3ng C\xF4ng ngh\u1EC7, Kinh t\u1EBF v\xE0 Ch\u1EBF bi\u1EBFn l\xE2m s\u1EA3n",
        code: "CDT2401",
        type: "Cao \u0111\u1EB3ng",
        sourceRow: 191,
        reported: {
          total: 689,
          college: 93,
          intermediate: 546,
          elementary: 50
        },
        rawCounts: [
          689,
          93,
          546,
          50
        ],
        groups: [
          {
            label: "H\u1EC7 cao \u0111\u1EB3ng",
            level: "Cao \u0111\u1EB3ng",
            sourceRow: 192,
            reported: {
              total: 0,
              college: null,
              intermediate: null,
              elementary: null
            },
            rawCounts: [
              0,
              null,
              null,
              null
            ]
          },
          {
            label: "H\u1EC7 trung c\u1EA5p",
            level: "Trung c\u1EA5p",
            sourceRow: 201,
            reported: {
              total: 0,
              college: null,
              intermediate: null,
              elementary: null
            },
            rawCounts: [
              0,
              null,
              null,
              null
            ]
          },
          {
            label: "H\u1EC7 s\u01A1 c\u1EA5p",
            level: "S\u01A1 c\u1EA5p",
            sourceRow: 211,
            reported: {
              total: 50,
              college: null,
              intermediate: null,
              elementary: 50
            },
            rawCounts: [
              50,
              null,
              null,
              50
            ]
          }
        ],
        programs: [
          {
            id: "program-r193",
            name: "Gia c\xF4ng & thi\u1EBFt k\u1EBF SP M\u1ED9c",
            code: "6210422",
            sourceRow: 193,
            levels: [
              "Cao \u0111\u1EB3ng"
            ],
            reported: {
              total: 10,
              college: 10,
              intermediate: null,
              elementary: null
            },
            rawCounts: [
              10,
              10,
              null,
              null
            ]
          },
          {
            id: "program-r194",
            name: "K\u1EF9 thu\u1EADt \u0110i\xEAu kh\u1EAFc g\u1ED7",
            code: "6210101",
            sourceRow: 194,
            levels: [
              "Cao \u0111\u1EB3ng"
            ],
            reported: {
              total: 10,
              college: 10,
              intermediate: null,
              elementary: null
            },
            rawCounts: [
              10,
              10,
              null,
              null
            ]
          },
          {
            id: "program-r195",
            name: "K\u1EF9 thu\u1EADt S\u01A1n m\xE0i v\xE0 Kh\u1EA3m trai",
            code: "6210412",
            sourceRow: 195,
            levels: [
              "Cao \u0111\u1EB3ng"
            ],
            reported: {
              total: 10,
              college: 10,
              intermediate: null,
              elementary: null
            },
            rawCounts: [
              10,
              10,
              null,
              null
            ]
          },
          {
            id: "program-r196",
            name: "K\u1EBF to\xE1n Doanh nghi\u1EC7p",
            code: "6340302",
            sourceRow: 196,
            levels: [
              "Cao \u0111\u1EB3ng"
            ],
            reported: {
              total: 10,
              college: 10,
              intermediate: null,
              elementary: null
            },
            rawCounts: [
              10,
              10,
              null,
              null
            ]
          },
          {
            id: "program-r197",
            name: "Qu\u1EA3n tr\u1ECB m\u1EA1ng m\xE1y t\xEDnh",
            code: "6480209",
            sourceRow: 197,
            levels: [
              "Cao \u0111\u1EB3ng"
            ],
            reported: {
              total: 20,
              college: 20,
              intermediate: null,
              elementary: null
            },
            rawCounts: [
              20,
              20,
              null,
              null
            ]
          },
          {
            id: "program-r198",
            name: "\u0110i\u1EC7n d\xE2n d\u1EE5ng",
            code: "6520226",
            sourceRow: 198,
            levels: [
              "Cao \u0111\u1EB3ng"
            ],
            reported: {
              total: 10,
              college: 10,
              intermediate: null,
              elementary: null
            },
            rawCounts: [
              10,
              10,
              null,
              null
            ]
          },
          {
            id: "program-r199",
            name: "\u0110i\u1EC7n C\xF4ng nghi\u1EC7p",
            code: "6520227",
            sourceRow: 199,
            levels: [
              "Cao \u0111\u1EB3ng"
            ],
            reported: {
              total: 11,
              college: 11,
              intermediate: null,
              elementary: null
            },
            rawCounts: [
              11,
              11,
              null,
              null
            ]
          },
          {
            id: "program-r200",
            name: "K\u1EF9 thu\u1EADt m\xE1y l\u1EA1nh v\xE0 \u0110HKK",
            code: "6520205",
            sourceRow: 200,
            levels: [
              "Cao \u0111\u1EB3ng"
            ],
            reported: {
              total: 12,
              college: 12,
              intermediate: null,
              elementary: null
            },
            rawCounts: [
              12,
              12,
              null,
              null
            ]
          },
          {
            id: "program-r202",
            name: "Gia c\xF4ng & thi\u1EBFt k\u1EBF SP M\u1ED9c",
            code: "5210422",
            sourceRow: 202,
            levels: [
              "Trung c\u1EA5p"
            ],
            reported: {
              total: 90,
              college: null,
              intermediate: 90,
              elementary: null
            },
            rawCounts: [
              90,
              null,
              90,
              null
            ]
          },
          {
            id: "program-r203",
            name: "K\u1EF9 thu\u1EADt \u0110i\xEAu kh\u1EAFc g\u1ED7",
            code: "5210101",
            sourceRow: 203,
            levels: [
              "Trung c\u1EA5p"
            ],
            reported: {
              total: 90,
              college: null,
              intermediate: 90,
              elementary: null
            },
            rawCounts: [
              90,
              null,
              90,
              null
            ]
          },
          {
            id: "program-r204",
            name: "K\u1EF9 thu\u1EADt S\u01A1n m\xE0i v\xE0 Kh\u1EA3m trai",
            code: "5210412",
            sourceRow: 204,
            levels: [
              "Trung c\u1EA5p"
            ],
            reported: {
              total: 60,
              college: null,
              intermediate: 60,
              elementary: null
            },
            rawCounts: [
              60,
              null,
              60,
              null
            ]
          },
          {
            id: "program-r205",
            name: "K\u1EBF to\xE1n Doanh nghi\u1EC7p",
            code: "5340302",
            sourceRow: 205,
            levels: [
              "Trung c\u1EA5p"
            ],
            reported: {
              total: 56,
              college: null,
              intermediate: 56,
              elementary: null
            },
            rawCounts: [
              56,
              null,
              56,
              null
            ]
          },
          {
            id: "program-r206",
            name: "Qu\u1EA3n tr\u1ECB m\u1EA1ng m\xE1y t\xEDnh",
            code: "5480209",
            sourceRow: 206,
            levels: [
              "Trung c\u1EA5p"
            ],
            reported: {
              total: 50,
              college: null,
              intermediate: 50,
              elementary: null
            },
            rawCounts: [
              50,
              null,
              50,
              null
            ]
          },
          {
            id: "program-r207",
            name: "Qu\u1EA3n tr\u1ECB CSDL",
            code: "5480208",
            sourceRow: 207,
            levels: [
              "Trung c\u1EA5p"
            ],
            reported: {
              total: 50,
              college: null,
              intermediate: 50,
              elementary: null
            },
            rawCounts: [
              50,
              null,
              50,
              null
            ]
          },
          {
            id: "program-r208",
            name: "\u0110i\u1EC7n d\xE2n d\u1EE5ng",
            code: "5520226",
            sourceRow: 208,
            levels: [
              "Trung c\u1EA5p"
            ],
            reported: {
              total: 50,
              college: null,
              intermediate: 50,
              elementary: null
            },
            rawCounts: [
              50,
              null,
              50,
              null
            ]
          },
          {
            id: "program-r209",
            name: "\u0110i\u1EC7n C\xF4ng nghi\u1EC7p",
            code: "5520227",
            sourceRow: 209,
            levels: [
              "Trung c\u1EA5p"
            ],
            reported: {
              total: 60,
              college: null,
              intermediate: 60,
              elementary: null
            },
            rawCounts: [
              60,
              null,
              60,
              null
            ]
          },
          {
            id: "program-r210",
            name: "K\u1EF9 thu\u1EADt m\xE1y l\u1EA1nh v\xE0 \u0110HKK",
            code: "5520205",
            sourceRow: 210,
            levels: [
              "Trung c\u1EA5p"
            ],
            reported: {
              total: 40,
              college: null,
              intermediate: 40,
              elementary: null
            },
            rawCounts: [
              40,
              null,
              40,
              null
            ]
          }
        ],
        issues: [
          "D\xF2ng 192 ghi H\u1EC7 cao \u0111\u1EB3ng b\u1EB1ng 0 trong khi d\xF2ng ngh\u1EC1 c\xF3 s\u1ED1 li\u1EC7u.",
          "D\xF2ng 201 ghi H\u1EC7 trung c\u1EA5p b\u1EB1ng 0 trong khi d\xF2ng ngh\u1EC1 c\xF3 s\u1ED1 li\u1EC7u."
        ]
      },
      {
        id: "school-r215",
        name: "Tr\u01B0\u1EDDng Trung c\u1EA5p C\xF4ng ngh\u1EC7 s\u1ED1 8 Nam \u0110\u1ECBnh",
        code: "TCD2502",
        type: "Trung c\u1EA5p",
        sourceRow: 215,
        reported: {
          total: 690,
          college: 0,
          intermediate: 550,
          elementary: 140
        },
        rawCounts: [
          690,
          0,
          550,
          140
        ],
        groups: [],
        programs: [
          {
            id: "program-r216",
            name: "C\xF4ng ngh\u1EC7 th\xF4ng tin",
            code: "5480201",
            sourceRow: 216,
            levels: [
              "Trung c\u1EA5p",
              "S\u01A1 c\u1EA5p"
            ],
            reported: {
              total: 125,
              college: null,
              intermediate: 90,
              elementary: 35
            },
            rawCounts: [
              125,
              null,
              90,
              35
            ]
          },
          {
            id: "program-r217",
            name: "C\xF4ng ngh\u1EC7 k\u1EF9 thu\u1EADt \xF4 t\xF4",
            code: "5510202",
            sourceRow: 217,
            levels: [
              "Trung c\u1EA5p"
            ],
            reported: {
              total: 40,
              college: null,
              intermediate: 40,
              elementary: 0
            },
            rawCounts: [
              40,
              null,
              40,
              0
            ]
          },
          {
            id: "program-r218",
            name: "C\xF4ng ngh\u1EC7, k\u1EF9 thu\u1EADt \u0111i\u1EC7n, \u0111i\u1EC7n t\u1EED",
            code: "5510303",
            sourceRow: 218,
            levels: [
              "Trung c\u1EA5p",
              "S\u01A1 c\u1EA5p"
            ],
            reported: {
              total: 160,
              college: null,
              intermediate: 125,
              elementary: 35
            },
            rawCounts: [
              160,
              null,
              125,
              35
            ]
          },
          {
            id: "program-r219",
            name: "H\xE0n",
            code: "5520123",
            sourceRow: 219,
            levels: [
              "Trung c\u1EA5p",
              "S\u01A1 c\u1EA5p"
            ],
            reported: {
              total: 105,
              college: null,
              intermediate: 70,
              elementary: 35
            },
            rawCounts: [
              105,
              null,
              70,
              35
            ]
          },
          {
            id: "program-r220",
            name: "May th\u1EDDi trang",
            code: "5540205",
            sourceRow: 220,
            levels: [
              "Trung c\u1EA5p",
              "S\u01A1 c\u1EA5p"
            ],
            reported: {
              total: 260,
              college: null,
              intermediate: 225,
              elementary: 35
            },
            rawCounts: [
              260,
              null,
              225,
              35
            ]
          }
        ],
        issues: []
      },
      {
        id: "school-r222",
        name: "Tr\u01B0\u1EDDng Trung c\u1EA5p GTVT N\u0110",
        code: "TCD2506",
        type: "Trung c\u1EA5p",
        sourceRow: 222,
        reported: {
          total: 250,
          college: 0,
          intermediate: null,
          elementary: 0
        },
        rawCounts: [
          250,
          0,
          null,
          0
        ],
        groups: [],
        programs: [
          {
            id: "program-r223",
            name: "C\xF4ng ngh\u1EC7 \xF4 t\xF4",
            code: "55102016",
            sourceRow: 223,
            levels: [
              "Trung c\u1EA5p"
            ],
            reported: {
              total: 153,
              college: null,
              intermediate: 153,
              elementary: null
            },
            rawCounts: [
              153,
              null,
              153,
              null
            ]
          },
          {
            id: "program-r224",
            name: "\u0110i\u1EC7n C\xF4ng nghi\u1EC7p",
            code: "5520227",
            sourceRow: 224,
            levels: [
              "Trung c\u1EA5p"
            ],
            reported: {
              total: 97,
              college: null,
              intermediate: 97,
              elementary: null
            },
            rawCounts: [
              97,
              null,
              97,
              null
            ]
          }
        ],
        issues: []
      },
      {
        id: "school-r225",
        name: "Tr\u01B0\u1EDDng Trung c\u1EA5p \u0110\u1EA1i L\xE2m",
        code: "TCD2503",
        type: "Trung c\u1EA5p",
        sourceRow: 225,
        reported: {
          total: 0,
          college: 0,
          intermediate: null,
          elementary: 0
        },
        rawCounts: [
          0,
          0,
          null,
          0
        ],
        groups: [],
        programs: [
          {
            id: "program-r226",
            name: "C\xF4ng ngh\u1EC7 \xF4t \xF4",
            code: "5510216",
            sourceRow: 226,
            levels: [
              "Trung c\u1EA5p"
            ],
            reported: {
              total: null,
              college: null,
              intermediate: 175,
              elementary: null
            },
            rawCounts: [
              null,
              null,
              175,
              null
            ]
          },
          {
            id: "program-r227",
            name: "May th\u1EDDi trang",
            code: "5540205",
            sourceRow: 227,
            levels: [
              "Trung c\u1EA5p"
            ],
            reported: {
              total: null,
              college: null,
              intermediate: 245,
              elementary: null
            },
            rawCounts: [
              null,
              null,
              245,
              null
            ]
          },
          {
            id: "program-r228",
            name: "K\u1EF9 thu\u1EADt ch\u1EBF bi\u1EBFn m\xF3n \u0103n",
            code: "5810207",
            sourceRow: 228,
            levels: [
              "Trung c\u1EA5p"
            ],
            reported: {
              total: null,
              college: null,
              intermediate: 30,
              elementary: null
            },
            rawCounts: [
              null,
              null,
              30,
              null
            ]
          }
        ],
        issues: [
          "C\u1ED9t T\u1ED5ng ghi 0 trong khi c\xE1c d\xF2ng ng\xE0nh, ngh\u1EC1 c\xF3 s\u1ED1 li\u1EC7u d\u01B0\u01A1ng."
        ]
      },
      {
        id: "school-r229",
        name: "Tr\u01B0\u1EDDng Trung C\u1EA5p K\u1EF9 Thu\u1EADt - Du L\u1ECBch C\xF4ng \u0110o\xE0n Ninh B\xECnh",
        code: "",
        type: "Trung c\u1EA5p",
        sourceRow: 229,
        reported: {
          total: null,
          college: null,
          intermediate: 300,
          elementary: null
        },
        rawCounts: [
          null,
          null,
          300,
          null
        ],
        groups: [],
        programs: [
          {
            id: "program-r230",
            name: "Ngh\u1EC1 \u0110i\u1EC7n d\xE2n d\u1EE5ng",
            code: "5520226",
            sourceRow: 230,
            levels: [
              "Trung c\u1EA5p"
            ],
            reported: {
              total: null,
              college: null,
              intermediate: 30,
              elementary: null
            },
            rawCounts: [
              null,
              null,
              30,
              null
            ]
          },
          {
            id: "program-r231",
            name: "Ngh\u1EC1 \u0110i\u1EC7n c\xF4ng nghi\u1EC7p v\xE0 d\xE2n d\u1EE5ng",
            code: "5520223",
            sourceRow: 231,
            levels: [
              "Trung c\u1EA5p"
            ],
            reported: {
              total: null,
              college: null,
              intermediate: 60,
              elementary: null
            },
            rawCounts: [
              null,
              null,
              60,
              null
            ]
          },
          {
            id: "program-r232",
            name: "Ngh\u1EC1 c\xF4ng ngh\u1EC7 th\xF4ng tin",
            code: "5480202",
            sourceRow: 232,
            levels: [
              "Trung c\u1EA5p"
            ],
            reported: {
              total: null,
              college: null,
              intermediate: 30,
              elementary: null
            },
            rawCounts: [
              null,
              null,
              30,
              null
            ]
          },
          {
            id: "program-r233",
            name: "Ngh\u1EC1 h\u01B0\u1EDBng d\u1EABn du l\u1ECBch",
            code: "5810103",
            sourceRow: 233,
            levels: [
              "Trung c\u1EA5p"
            ],
            reported: {
              total: null,
              college: null,
              intermediate: 60,
              elementary: null
            },
            rawCounts: [
              null,
              null,
              60,
              null
            ]
          },
          {
            id: "program-r234",
            name: "Qu\u1EA3n tr\u1ECB kh\xE1ch s\u1EA1n",
            code: "5810201",
            sourceRow: 234,
            levels: [
              "Trung c\u1EA5p"
            ],
            reported: {
              total: null,
              college: null,
              intermediate: 30,
              elementary: null
            },
            rawCounts: [
              null,
              null,
              30,
              null
            ]
          },
          {
            id: "program-r235",
            name: "Nghi\u1EC7p v\u1EE5 nh\xE0 h\xE0ng",
            code: "5810206",
            sourceRow: 235,
            levels: [
              "Trung c\u1EA5p"
            ],
            reported: {
              total: null,
              college: null,
              intermediate: 30,
              elementary: null
            },
            rawCounts: [
              null,
              null,
              30,
              null
            ]
          },
          {
            id: "program-r236",
            name: "Ngh\u1EC1 may th\u1EDDi trang",
            code: "5540205",
            sourceRow: 236,
            levels: [
              "Trung c\u1EA5p"
            ],
            reported: {
              total: null,
              college: null,
              intermediate: 30,
              elementary: null
            },
            rawCounts: [
              null,
              null,
              30,
              null
            ]
          },
          {
            id: "program-r237",
            name: "K\u1EF9 thu\u1EADt ch\u1EBF bi\u1EBFn m\xF3n \u0103n",
            code: "5810207",
            sourceRow: 237,
            levels: [
              "Trung c\u1EA5p"
            ],
            reported: {
              total: null,
              college: null,
              intermediate: 30,
              elementary: null
            },
            rawCounts: [
              null,
              null,
              30,
              null
            ]
          }
        ],
        issues: []
      }
    ]
  };

  // lib/training-schools.ts
  var TRAINING_SOURCE = training_schools_default.source;
  var SCHOOL_CATALOG_REVISION = "2026-10-01-v1-" + TRAINING_SOURCE.sha256.slice(0, 16);
  var BASE_PROFESSIONS = ["C\u01A1 \u0111i\u1EC7n t\u1EED", "\u0110i\u1EC7n c\xF4ng nghi\u1EC7p", "C\u1EAFt g\u1ECDt kim lo\u1EA1i", "May c\xF4ng nghi\u1EC7p", "Nghi\u1EC7p v\u1EE5 nh\xE0 h\xE0ng", "Tin h\u1ECDc \u1EE9ng d\u1EE5ng"];
  function normalizeTraining(value) {
    return value.normalize("NFD").replace(/[\u0300-\u036f]/g, "").replace(/đ/g, "d").replace(/Đ/g, "D").toLowerCase().trim();
  }
  var nameKey = (value) => normalizeTraining(value).replace(/[\s,.-]+/g, "");
  var professionNames = /* @__PURE__ */ new Map();
  for (const name of [...BASE_PROFESSIONS, ...training_schools_default.schools.flatMap((s) => s.programs.map((p) => p.name))]) if (!professionNames.has(nameKey(name))) professionNames.set(nameKey(name), name);
  var ALL_PROFESSIONS = [...professionNames.values()];
  var TRAINING_PROFESSIONS = [...new Set(training_schools_default.schools.flatMap((s) => s.programs.map((p) => professionNames.get(nameKey(p.name)))))].sort((a, b) => a.localeCompare(b, "vi"));
  function canonicalProfession(name) {
    return professionNames.get(nameKey(name)) ?? name;
  }
  var TRAINING_SCHOOLS = training_schools_default.schools.map((s) => ({ ...s, programs: s.programs.map((p) => ({ ...p, profession: canonicalProfession(p.name) })) }));

  // lib/portal-data.ts
  var PROFESSIONS = ALL_PROFESSIONS;
  var LEVELS = ["S\u01A1 c\u1EA5p", "Trung c\u1EA5p", "Cao \u0111\u1EB3ng", "D\u01B0\u1EDBi 3 th\xE1ng"];
  var PERIODS = ["Qu\xFD IV/2026", "Qu\xFD I/2027"];
  var PURPOSES = ["Tuy\u1EC3n d\u1EE5ng", "\u0110\xE0o t\u1EA1o m\u1EDBi", "\u0110\xE0o t\u1EA1o l\u1EA1i"];
  var listingSchema = external_exports.object({
    kind: external_exports.enum(["demand", "capacity"]),
    org: external_exports.string().trim().min(3).max(160),
    profession: external_exports.enum(PROFESSIONS),
    level: external_exports.enum(LEVELS),
    period: external_exports.enum(PERIODS),
    quantity: external_exports.number().int().min(1).max(1e5),
    skills: external_exports.array(external_exports.string().trim().min(1).max(80)).min(1).max(20),
    district: external_exports.string().trim().min(2).max(120),
    purpose: external_exports.enum(PURPOSES).default("Tuy\u1EC3n d\u1EE5ng"),
    age: external_exports.string().trim().max(80).default(""),
    salary: external_exports.string().trim().max(100).default(""),
    duration: external_exports.string().trim().max(100).default(""),
    equipment: external_exports.string().trim().max(1e3).default(""),
    note: external_exports.string().trim().max(2e3).default("")
  });
  var proposalSchema = external_exports.object({ demandId: external_exports.string().min(1).max(100), capacityId: external_exports.string().min(1).max(100), quantity: external_exports.number().int().min(1).max(1e5), plan: external_exports.string().trim().min(10).max(2e3) });
  var emptyFilters = { profession: "all", level: "all", period: PERIODS[0], query: "" };
  function normalize(v) {
    return v.normalize("NFD").replace(/[\u0300-\u036f]/g, "").replace(/đ/g, "d").replace(/Đ/g, "D").toLowerCase();
  }
  function filtered(data, f) {
    return data.listings.filter((r) => (f.profession === "all" || r.profession === f.profession) && (f.level === "all" || r.level === f.level) && r.period === f.period && (!f.query || normalize([r.org, r.profession, r.district, ...r.skills].join(" ")).includes(normalize(f.query))));
  }
  function matches(d, c) {
    return d.kind === "demand" && c.kind === "capacity" && d.profession === c.profession && d.level === c.level && d.period === c.period;
  }
  function remaining(r, proposals) {
    return Math.max(0, r.quantity - proposals.filter((p) => r.kind === "demand" ? p.demandId === r.id : p.capacityId === r.id).reduce((s, p) => s + p.quantity, 0));
  }
  function skillGap(d, c) {
    return d.skills.filter((s) => !c.skills.some((k) => normalize(k) === normalize(s)));
  }
  function summary(data, f) {
    const scope = filtered(data, { ...f, query: "" });
    const keys = new Set(filtered(data, f).map((r) => r.profession + "|" + r.level));
    const groups = [...new Set(scope.map((r) => r.profession + "|" + r.level))].filter((k) => keys.has(k));
    return groups.map((key) => {
      const rows = scope.filter((r) => r.profession + "|" + r.level === key), d = rows.filter((r) => r.kind === "demand"), c = rows.filter((r) => r.kind === "capacity");
      const demand = d.reduce((s, r) => s + r.quantity, 0), capacity = c.reduce((s, r) => s + r.quantity, 0);
      const ids = new Set(d.map((r) => r.id));
      return { key, profession: rows[0].profession, level: rows[0].level, demand, capacity, gap: Math.max(0, demand - capacity), surplus: Math.max(0, capacity - demand), proposed: data.proposals.filter((p) => ids.has(p.demandId)).reduce((s, p) => s + p.quantity, 0), companies: new Set(d.map((r) => r.org)).size, schools: new Set(c.map((r) => r.org)).size };
    }).sort((a, b) => b.gap - a.gap || b.demand - a.demand);
  }
  function csv(rows, period) {
    const cell = (v) => {
      let s = String(v);
      if (/^[=+@\-\t\r]/.test(s)) s = "'" + s;
      return '"' + s.replace(/"/g, '""') + '"';
    };
    return "\uFEFF" + [["K\u1EF3", "Ngh\u1EC1", "Tr\xECnh \u0111\u1ED9", "Nhu c\u1EA7u (ng\u01B0\u1EDDi)", "N\u0103ng l\u1EF1c d\u1EF1 ki\u1EBFn (ng\u01B0\u1EDDi)", "Ch\xEAnh l\u1EC7ch thi\u1EBFu (ng\u01B0\u1EDDi)", "D\u01B0 n\u0103ng l\u1EF1c (ng\u01B0\u1EDDi)", "\u0110\xE3 \u0111\u1EC1 xu\u1EA5t (ng\u01B0\u1EDDi)", "S\u1ED1 doanh nghi\u1EC7p", "S\u1ED1 c\u01A1 s\u1EDF"], ...rows.map((r) => [period, r.profession, r.level, r.demand, r.capacity, r.gap, r.surplus, r.proposed, r.companies, r.schools])].map((row) => row.map(cell).join(",")).join("\r\n");
  }
  var base = { period: PERIODS[0], purpose: "Tuy\u1EC3n d\u1EE5ng", age: "T\u1EEB 18 tu\u1ED5i", salary: "Th\u1ECFa thu\u1EADn (minh h\u1ECDa)", duration: "", equipment: "", note: "D\u1EEF li\u1EC7u gi\u1EA3 \u0111\u1ECBnh \u0111\u1EC3 th\u1EED c\u1ED5ng; kh\xF4ng ph\u1EA3i th\xF4ng b\xE1o tuy\u1EC3n d\u1EE5ng ho\u1EB7c tuy\u1EC3n sinh.", sample: 1, createdAt: "2026-10-01T02:00:00.000Z" };
  function demo(id, kind, org, profession, level, quantity, skills, district, extra = {}) {
    return { ...base, id, kind, org, profession, level, quantity, skills, district, ...extra };
  }
  var SEED = { listings: [
    demo("d1", "demand", "Doanh nghi\u1EC7p \u0110i\u1EC7n t\u1EED A (m\u1EABu)", "C\u01A1 \u0111i\u1EC7n t\u1EED", "Cao \u0111\u1EB3ng", 180, ["PLC", "L\u1EAFp r\xE1p", "Ti\u1EBFng Trung c\u01A1 b\u1EA3n"], "Khu v\u1EF1c H\xE0 Nam"),
    demo("d2", "demand", "Doanh nghi\u1EC7p C\u01A1 kh\xED B (m\u1EABu)", "\u0110i\u1EC7n c\xF4ng nghi\u1EC7p", "Trung c\u1EA5p", 120, ["An to\xE0n \u0111i\u1EC7n", "B\u1EA3o tr\xEC", "\u0110\u1ECDc b\u1EA3n v\u1EBD"], "Khu v\u1EF1c Ninh B\xECnh"),
    demo("d3", "demand", "Doanh nghi\u1EC7p C\u01A1 kh\xED B (m\u1EABu)", "C\u1EAFt g\u1ECDt kim lo\u1EA1i", "Trung c\u1EA5p", 80, ["CNC", "\u0110\u1ECDc b\u1EA3n v\u1EBD", "\u0110o ki\u1EC3m"], "Khu v\u1EF1c Ninh B\xECnh"),
    demo("d4", "demand", "Doanh nghi\u1EC7p D\u1EC7t may C (m\u1EABu)", "May c\xF4ng nghi\u1EC7p", "S\u01A1 c\u1EA5p", 240, ["May c\xF4ng nghi\u1EC7p", "Ki\u1EC3m tra ch\u1EA5t l\u01B0\u1EE3ng"], "Khu v\u1EF1c Nam \u0110\u1ECBnh"),
    demo("d5", "demand", "Doanh nghi\u1EC7p Du l\u1ECBch D (m\u1EABu)", "Nghi\u1EC7p v\u1EE5 nh\xE0 h\xE0ng", "S\u01A1 c\u1EA5p", 60, ["Ph\u1EE5c v\u1EE5 b\xE0n", "Ti\u1EBFng Anh giao ti\u1EBFp"], "Khu v\u1EF1c Ninh B\xECnh"),
    demo("d6", "demand", "Doanh nghi\u1EC7p D\u1ECBch v\u1EE5 E (m\u1EABu)", "Tin h\u1ECDc \u1EE9ng d\u1EE5ng", "D\u01B0\u1EDBi 3 th\xE1ng", 50, ["Excel", "K\u1EF9 n\u0103ng s\u1ED1"], "Khu v\u1EF1c Nam \u0110\u1ECBnh", { purpose: "\u0110\xE0o t\u1EA1o l\u1EA1i" }),
    demo("d7", "demand", "Doanh nghi\u1EC7p \u0110i\u1EC7n t\u1EED A (m\u1EABu)", "\u0110i\u1EC7n c\xF4ng nghi\u1EC7p", "Trung c\u1EA5p", 70, ["B\u1EA3o tr\xEC", "An to\xE0n \u0111i\u1EC7n"], "Khu v\u1EF1c H\xE0 Nam", { purpose: "\u0110\xE0o t\u1EA1o m\u1EDBi" }),
    demo("d8", "demand", "Doanh nghi\u1EC7p \u0110i\u1EC7n t\u1EED F (m\u1EABu)", "C\u01A1 \u0111i\u1EC7n t\u1EED", "Cao \u0111\u1EB3ng", 100, ["PLC", "Robot"], "Khu v\u1EF1c H\xE0 Nam", { period: PERIODS[1] }),
    demo("c1", "capacity", "C\u01A1 s\u1EDF GDNN A (m\u1EABu)", "C\u01A1 \u0111i\u1EC7n t\u1EED", "Cao \u0111\u1EB3ng", 100, ["PLC", "L\u1EAFp r\xE1p"], "Khu v\u1EF1c H\xE0 Nam", { duration: "B\u1ED5 sung k\u1EF9 n\u0103ng: 8 tu\u1EA7n", equipment: "X\u01B0\u1EDFng c\u01A1 \u0111i\u1EC7n t\u1EED, m\xF4 h\xECnh PLC; 6 gi\u1EA3ng vi\xEAn" }),
    demo("c2", "capacity", "C\u01A1 s\u1EDF GDNN B (m\u1EABu)", "\u0110i\u1EC7n c\xF4ng nghi\u1EC7p", "Trung c\u1EA5p", 130, ["An to\xE0n \u0111i\u1EC7n", "B\u1EA3o tr\xEC", "\u0110\u1ECDc b\u1EA3n v\u1EBD"], "Khu v\u1EF1c Ninh B\xECnh", { duration: "B\u1ED5 sung k\u1EF9 n\u0103ng: 6 tu\u1EA7n", equipment: "X\u01B0\u1EDFng \u0111i\u1EC7n c\xF4ng nghi\u1EC7p; 8 gi\u1EA3ng vi\xEAn" }),
    demo("c3", "capacity", "C\u01A1 s\u1EDF GDNN B (m\u1EABu)", "C\u1EAFt g\u1ECDt kim lo\u1EA1i", "Trung c\u1EA5p", 50, ["CNC", "\u0110\u1ECDc b\u1EA3n v\u1EBD", "\u0110o ki\u1EC3m"], "Khu v\u1EF1c Ninh B\xECnh", { duration: "B\u1ED5 sung k\u1EF9 n\u0103ng: 10 tu\u1EA7n", equipment: "4 m\xE1y CNC, ph\xF2ng \u0111o ki\u1EC3m; 5 gi\u1EA3ng vi\xEAn" }),
    demo("c4", "capacity", "C\u01A1 s\u1EDF GDNN C (m\u1EABu)", "May c\xF4ng nghi\u1EC7p", "S\u01A1 c\u1EA5p", 180, ["May c\xF4ng nghi\u1EC7p", "Ki\u1EC3m tra ch\u1EA5t l\u01B0\u1EE3ng"], "Khu v\u1EF1c Nam \u0110\u1ECBnh", { duration: "Kh\xF3a s\u01A1 c\u1EA5p: 3 th\xE1ng", equipment: "60 m\xE1y may c\xF4ng nghi\u1EC7p; 6 gi\u1EA3ng vi\xEAn" }),
    demo("c5", "capacity", "C\u01A1 s\u1EDF GDNN D (m\u1EABu)", "Nghi\u1EC7p v\u1EE5 nh\xE0 h\xE0ng", "S\u01A1 c\u1EA5p", 80, ["Ph\u1EE5c v\u1EE5 b\xE0n", "Ti\u1EBFng Anh giao ti\u1EBFp"], "Khu v\u1EF1c Ninh B\xECnh", { duration: "Kh\xF3a s\u01A1 c\u1EA5p: 3 th\xE1ng", equipment: "Ph\xF2ng th\u1EF1c h\xE0nh nh\xE0 h\xE0ng; 4 gi\u1EA3ng vi\xEAn" }),
    demo("c6", "capacity", "C\u01A1 s\u1EDF GDNN C (m\u1EABu)", "Tin h\u1ECDc \u1EE9ng d\u1EE5ng", "D\u01B0\u1EDBi 3 th\xE1ng", 60, ["Excel", "K\u1EF9 n\u0103ng s\u1ED1"], "Khu v\u1EF1c Nam \u0110\u1ECBnh", { duration: "Kh\xF3a k\u1EF9 n\u0103ng: 4 tu\u1EA7n", equipment: "2 ph\xF2ng m\xE1y t\xEDnh; 4 gi\u1EA3ng vi\xEAn" }),
    demo("c7", "capacity", "C\u01A1 s\u1EDF GDNN A (m\u1EABu)", "C\u01A1 \u0111i\u1EC7n t\u1EED", "Cao \u0111\u1EB3ng", 120, ["PLC", "Robot"], "Khu v\u1EF1c H\xE0 Nam", { period: PERIODS[1], duration: "B\u1ED5 sung k\u1EF9 n\u0103ng: 8 tu\u1EA7n", equipment: "Ph\xF2ng th\u1EF1c h\xE0nh robot; 6 gi\u1EA3ng vi\xEAn" })
  ], proposals: [{ id: "p1", demandId: "d2", capacityId: "c2", quantity: 80, plan: "Ph\u1ED1i h\u1EE3p kh\u1EA3o s\xE1t k\u1EF9 n\u0103ng; b\u1ED5 sung an to\xE0n \u0111i\u1EC7n v\xE0 b\u1EA3o tr\xEC tr\u01B0\u1EDBc khi \u0111\xE1nh gi\xE1 t\u1EA1i doanh nghi\u1EC7p.", sample: 1, createdAt: base.createdAt }, { id: "p2", demandId: "d4", capacityId: "c4", quantity: 100, plan: "T\u1ED5 ch\u1EE9c l\u1EDBp theo \u0111\u01A1n \u0111\u1EB7t h\xE0ng, th\u1EF1c h\xE0nh may v\xE0 ki\u1EC3m tra ch\u1EA5t l\u01B0\u1EE3ng t\u1EA1i x\u01B0\u1EDFng.", sample: 1, createdAt: base.createdAt }] };
  return __toCommonJS(rules_entry_exports);
})();
