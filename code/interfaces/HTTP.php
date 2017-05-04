<?php

namespace Modular\Interfaces;

interface HTTP {
	const OKContinue_100                    = 100;
	const SwitchingProtocols_101            = 101;
	const Processing_102                    = 102;
	const OK_200                            = 200;
	const Created_201                       = 201;
	const Accepted_202                      = 202;
	const NonAuthoritativeInformation_203   = 203;
	const NoContent_204                     = 204;
	const ResetContent_205                  = 205;
	const PartialContent_206                = 206;
	const MultiStatus_207                   = 207;
	const AlreadyReported_208               = 208;
	const IMUsed_226                        = 226;
	const MultipleChoices_300               = 300;
	const MovedPermanently_301              = 301;
	const Found_302                         = 302;
	const SeeOther_303                      = 303;
	const NotModified_304                   = 304;
	const UseProxy_305                      = 305;
	const TemporaryRedirect_307             = 307;
	const PermanentRedirect_308             = 308;
	const BadRequest_400                    = 400;
	const Unauthorized_401                  = 401;
	const PaymentRequired_402               = 402;
	const Forbidden_403                     = 403;
	const NotFound_404                      = 404;
	const MethodNotAllowed_405              = 405;
	const NotAcceptable_406                 = 406;
	const ProxyAuthenticationRequired_407   = 407;
	const RequestTimeout_408                = 408;
	const Conflict_409                      = 409;
	const Gone_410                          = 410;
	const LengthRequired_411                = 411;
	const PreconditionFailed_412            = 412;
	const RequestEntityTooLarge_413         = 413;
	const RequestURITooLong_414             = 414;
	const UnsupportedMediaType_415          = 415;
	const RequestedRangeNotSatisfiable_416  = 416;
	const ExpectationFailed_417             = 417;
	const UnprocessableEntity_422           = 422;
	const Locked_423                        = 423;
	const FailedDependency_424              = 424;
	const Reserved_425                      = 425;
	const UpgradeRequired_426               = 426;
	const PreconditionRequired_428          = 428;
	const TooManyRequests_429               = 429;
	const RequestHeaderFieldsTooLarge_431   = 431;
	const InternalServerError_500           = 500;
	const NotImplemented_501                = 501;
	const BadGateway_502                    = 502;
	const ServiceUnavailable_503            = 503;
	const GatewayTimeout_504                = 504;
	const HTTPVersionNotSupported_505       = 505;
	const VariantAlsoNegotiates_506         = 506;
	const InsufficientStorage_507           = 507;
	const LoopDetected_508                  = 508;
	const NotExtended_510                   = 510;
	const NetworkAuthenticationRequired_511 = 511;

	const PartScheme   = 'scheme';
	const PartHost     = 'host';
	const PartPort     = 'port';
	const PartUser     = 'user';
	const PartPassword = 'pass';
	const PartPath     = 'path';
	const PartQuery    = 'query';
	const PartFragment = 'fragment';

	const SchemeFile  = 'file';
	const SchemeHTTP  = 'http';
	const SchemeHTTPS = 'https';

	const EncodeRawURL = 'rawurlencode';
	const EncodeURL = 'urlencode';
	const EncodeEnitites = 'htmlentities';
	const EncodeSpaces = 'encodeSpaces';

	const QueryStringEncode = self::EncodeURL;

}