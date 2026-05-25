<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

function db_connect()
{
    static $conn = null;
    if ($conn === null) {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        $conn->set_charset('utf8mb4');
    }
    return $conn;
}

function get_param_types(array $params): string
{
    $types = '';
    foreach ($params as $param) {
        if (is_int($param)) {
            $types .= 'i';
        } elseif (is_float($param)) {
            $types .= 'd';
        } else {
            $types .= 's';
        }
    }
    return $types;
}

function ref_values(array &$arr): array
{
    $refs = [];
    foreach ($arr as $key => $value) {
        $refs[$key] = &$arr[$key];
    }
    return $refs;
}

function db_prepare(string $sql, array $params = [])
{
    $conn = db_connect();
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        throw new RuntimeException('Failed to prepare SQL statement.');
    }
    if (!empty($params)) {
        $types = get_param_types($params);
        call_user_func_array([$stmt, 'bind_param'], array_merge([$types], ref_values($params)));
    }
    return $stmt;
}

function db_fetch_all(string $sql, array $params = []): array
{
    $stmt = db_prepare($sql, $params);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $rows ?: [];
}

function db_fetch_one(string $sql, array $params = []): ?array
{
    $rows = db_fetch_all($sql, $params);
    return $rows[0] ?? null;
}

function db_execute(string $sql, array $params = []): int
{
    $stmt = db_prepare($sql, $params);
    $stmt->execute();
    $insertId = $stmt->insert_id;
    $affectedRows = $stmt->affected_rows;
    $stmt->close();
    return $insertId > 0 ? $insertId : $affectedRows;
}
