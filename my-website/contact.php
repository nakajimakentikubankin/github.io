<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// ==============================
// 設定
// ==============================
$to = "ryoshun111619@outlook.jp"; // 管理者メール
$subject = "【お問い合わせ】有限会社 中島建築鈑金";

// 文字コード設定
mb_language("Japanese");
mb_internal_encoding("UTF-8");

// ==============================
// POSTチェック
// ==============================
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
  header("Location: contact.html");
  exit;
}

// ==============================
// 入力値取得（XSS対策）
// ==============================
$name    = htmlspecialchars($_POST["name"] ?? "", ENT_QUOTES, "UTF-8");
$tel     = htmlspecialchars($_POST["tel"] ?? "", ENT_QUOTES, "UTF-8");
$email   = htmlspecialchars($_POST["email"] ?? "", ENT_QUOTES, "UTF-8");
$type    = htmlspecialchars($_POST["type"] ?? "", ENT_QUOTES, "UTF-8");
$message = htmlspecialchars($_POST["message"] ?? "", ENT_QUOTES, "UTF-8");

// ==============================
// 必須チェック
// ==============================
if ($name === "" || $email === "" || $message === "") {
  echo "必須項目が入力されていません。";
  exit;
}

// ==============================
// メール本文
// ==============================
$body = <<<EOT
以下のお問い合わせを受け付けました。

【お名前】
$name

【電話番号】
$tel

【メールアドレス】
$email

【お問い合わせ種別】
$type

【お問い合わせ内容】
$message

----------------------------------
有限会社 中島建築鈑金
EOT;

// ==============================
// ヘッダー（安全）
// ==============================
$headers  = "From: 中島建築鈑金 <{$to}>\r\n";
$headers .= "Reply-To: {$email}";

// ==============================
// メール送信
// ==============================
if (!mb_send_mail($to, $subject, $body, $headers)) {
  echo "メール送信に失敗しました。";
  exit;
}

// ==============================
// 自動返信
// ==============================
$reply_subject = "【受付完了】お問い合わせありがとうございます";
$reply_body = <<<EOT
{$name} 様

この度はお問い合わせいただき、誠にありがとうございます。
以下の内容でお問い合わせを受け付けました。

----------------------------------
$message
----------------------------------

内容を確認のうえ、担当者よりご連絡いたします。

有限会社 中島建築鈑金
TEL：027-223-2537
EOT;

$reply_headers = "From: 中島建築鈑金 <{$to}>";
mb_send_mail($email, $reply_subject, $reply_body, $reply_headers);

// ==============================
// 完了ページへ
// ==============================
header("Location: thanks.html");
exit;