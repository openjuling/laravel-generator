<?php

declare(strict_types=1);

if (! function_exists('create_guid')) {
    /**
     * 生成全局唯一标识符(GUID)
     *
     * @see Original: https://github.com/MicrosoftTranslator/Text-Translation-API-V3-PHP/blob/master/Translate.php#L26
     */
    function create_guid(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xFFFF),
            mt_rand(0, 0xFFFF),
            mt_rand(0, 0xFFFF),
            mt_rand(0, 0x0FFF) | 0x4000,
            mt_rand(0, 0x3FFF) | 0x8000,
            mt_rand(0, 0xFFFF),
            mt_rand(0, 0xFFFF),
            mt_rand(0, 0xFFFF)
        );
    }
}

if (! function_exists('is_email')) {
    /**
     * 验证邮箱地址格式
     */
    function is_email($email): bool
    {
        return ! (filter_var($email, FILTER_VALIDATE_EMAIL) === false);
    }
}

if (! function_exists('is_mobile')) {
    /**
     * 验证手机号码格式
     */
    function is_mobile($mobile): bool
    {
        $rule = '/^1[3-9]\d{9}$/';

        return is_scalar($mobile) && 1 === preg_match($rule, (string) $mobile);
    }
}
