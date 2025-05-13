<?php
// Включим вывод ошибок для отладки
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Получаем данные из формы
$name = $_POST['name'] ?? '';
$phone = $_POST['phone'] ?? '';
$email = $_POST['email'] ?? '';
$message = $_POST['message'] ?? '';

// Получаем выбранное оборудование (для чекбоксов)
$equipment = isset($_POST['equipment']) ? $_POST['equipment'] : [];
$equipmentText = !empty($equipment) ? implode(", ", $equipment) : "Не выбрано";

// Данные бота (проверьте эти значения!)
$token = "8173980684:AAHWG7LGvXeLXFx8DA5mo0hbX-Kmb6Avln4";
$chat_id = "-1002546039319";

// Формируем сообщение
$messageText = "
<b>👤 Имя:</b> $name
<b>📞 Телефон:</b> $phone
<b>📧 Email:</b> $email
<b>🛠 Оборудование:</b> $equipmentText
<b>✉️ Сообщение:</b> $message
";

// Кодируем сообщение для URL
$encodedMessage = urlencode($messageText);

// Формируем URL запроса
$apiUrl = "https://api.telegram.org/bot8173980684:AAHWG7LGvXeLXFx8DA5mo0hbX-Kmb6Avln4/sendMessage?chat_id=-1002546039319&parse_mode=HTML&text=$encodedMessage";

// Отправляем запрос через cURL (более надежный способ, чем file_get_contents)
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // На некоторых серверах требуется
$response = curl_exec($ch);

if (curl_errno($ch)) {
    die('Ошибка cURL: ' . curl_error($ch));
}

curl_close($ch);

// Декодируем ответ
$responseData = json_decode($response, true);

// Проверяем ответ
if (!$responseData || !isset($responseData['ok'])) {
    die("Неверный ответ от сервера Telegram");
}

if (!$responseData['ok']) {
    echo "<h2>Ошибка Telegram API:</h2>";
    echo "<pre>";
    print_r($responseData);
    echo "</pre>";
    echo "<p>URL запроса: $apiUrl</p>";
    exit;
}

// Если всё успешно - перенаправляем
header('Location: success.html');
exit;
?>