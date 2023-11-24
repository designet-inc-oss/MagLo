<?php
/* Internal errors */
define("NO_SUCH_FILE", "101");
$msg["ja"]["101"]["web"] = "ファイルが存在しません(%s)";
$msg["ja"]["101"]["log"] = "No such file.(%s)";

define("NO_READ_FILE", "102");
$msg["ja"]["102"]["web"] = "ファイルに読込権がありません(%s)";
$msg["ja"]["102"]["log"] = "Cannot read file.(%s)";

define("NO_WRITE_FILE", "103");
$msg["ja"]["103"]["web"] = "ファイルに書込権がありません(%s)";
$msg["ja"]["103"]["log"] = "Cannot write file.(%s)";

define("NO_EXEC_FILE", "104");
$msg["ja"]["104"]["web"] = "ファイルに実行権がありません(%s)";
$msg["ja"]["104"]["log"] = "Cannot exec file.(%s)";

define("NO_SUCH_DIR", "105");
$msg["ja"]["105"]["web"] = "ディレクトリが存在しません(%s)";
$msg["ja"]["105"]["log"] = "No such directory.(%s)";

define("NO_READ_DIR", "106");
$msg["ja"]["106"]["web"] = "ディレクトリに読込権がありません(%s)";
$msg["ja"]["106"]["log"] = "Cannot read directory.(%s)";

define("NO_WRITE_DIR", "107");
$msg["ja"]["107"]["web"] = "ディレクトリに書込権がありません(%s)";
$msg["ja"]["107"]["log"] = "Cannot write directory.(%s)";

define("INVALID_LOGFACILITY", "109");
$msg["ja"]["109"]["web"] = "ログファシリティの形式が不正です(%s)";
$msg["ja"]["109"]["log"] = "Invalid log facility.(%s)";

define("INVALID_MADDR", "110");
$msg["ja"]["110"]["web"] = "%sの形式が不正です(%s)";
$msg["ja"]["110"]["log"] = "Invalid mail address.(%s)";

define("INVALID_LENGTH", "111");
$msg["ja"]["111"]["web"] = "%sの文字列の長さが不正です(%s)";
$msg["ja"]["111"]["log"] = "Invalid length.";

define("NO_INPUT", "112");
$msg["ja"]["112"]["web"] = "値が入力されていません(%s)";
$msg["ja"]["112"]["log"] = "Value has not been entered.";

define("NOT_OPEN_DIR", "113");
$msg["ja"]["113"]["web"] = "ディレクトリを開けません(%s)";
$msg["ja"]["113"]["log"] = "Can not open the directory.(%s)";

define("FAIL_CREATE_DIR", "114");
$msg["ja"]["114"]["web"] = "ディレクトリの作成に失敗しました(%s)";
$msg["ja"]["114"]["log"] = "Failed to create directory.(%s)";

define("FAIL_DELETE_DIR", "115");
$msg["ja"]["115"]["web"] = "ディレクトリの削除に失敗しました(%s)";
$msg["ja"]["115"]["log"] = "Failed to delete directory.(%s)";


/* Config errors */
define("CONFIG_READ_ERROR", "150");
$msg["ja"]["150"]["web"] = "設定ファイルが読み込めません(%s)";
$msg["ja"]["150"]["log"] = "Cannot read config file.(%s)";

define("CONFIG_SYNTAX_ERROR", "151");
$msg["ja"]["151"]["web"] = "設定ファイルのフォーマットが異常です(%s)";
$msg["ja"]["151"]["log"] = "Invalid format.(%s)";

define("INVALID_CONFIGURATION", "152");
$msg["ja"]["152"]["web"] = "設定項目の値がありません(項目名:%s)";
$msg["ja"]["152"]["log"] = "Invalid configration.(%s : no value)";

define("CONFIG_SESS", "153");
$msg["ja"]["153"]["web"] = "SessionTimeoutの値が不正です(%s)";
$msg["ja"]["153"]["log"] = "Invalid configration: SessionTimeout.(%s)";

define("CONFIG_LOG", "154");
$msg["ja"]["154"]["web"] = "LogFacilityの値が不正です(%s)";
$msg["ja"]["154"]["log"] = "Invalid configration: LogFacility.(%s)";

define("CONFIG_SENDMAIL", "155");
$msg["ja"]["155"]["web"] = "Sendamilの値が不正です(%s)";
$msg["ja"]["155"]["log"] = "Invalid configration: Sendmail.(%s)";

define("CONFIG_FROM", "156");
$msg["ja"]["156"]["web"] = "FromDomainの値が不正です(%s)";
$msg["ja"]["156"]["log"] = "Invalid configration: FromDomain.(%s)";

define("CONFIG_ADMIN", "157");
$msg["ja"]["157"]["web"] = "AdminDirの値が不正です(%s)";
$msg["ja"]["157"]["log"] = "Invalid configration: AdminDir.(%s)";


/* Session errors */
define("SESSION_ERROR", "160");
$msg["ja"]["160"]["web"] = "不正な接続方法です。";
$msg["ja"]["160"]["log"] = "Invalid session.";

define("TIME_OUT", "161");
$msg["ja"]["161"]["web"] = "セッションがタイムアウトしました。";
$msg["ja"]["161"]["log"] = "Session timeout.";

define("DESTROY_SESSION_ERROR", "162");
$msg["ja"]["162"]["web"] = "セッションの破棄に失敗しました。";
$msg["ja"]["162"]["log"] = "Failed session destroy.";

define("READ_ADMIN_ERROR", "163");
$msg["ja"]["163"]["web"] = "管理者パスワードファイルが読み込めません(%s)";
$msg["ja"]["163"]["log"] = "Cannot read password file.(%s)";

define("ADMIN_INVALID", "164");
$msg["ja"]["164"]["web"] = "管理者パスワードファイルの値が不正です(%s行目)";
$msg["ja"]["164"]["log"] = "Invalid admin_pass_file.(line: %s)";

define("LOGOUT", "165");
$msg["ja"]["165"]["web"] = "ログアウトしました";
$msg["ja"]["165"]["log"] = "Logged out.";

define("SESSION_EMPTY", "166");
$msg["ja"]["166"]["web"] = "セッションがありません。";
$msg["ja"]["166"]["log"] = "Empty session.";


/* Login page errors */
define("INVALID_ID", "201");
$msg["ja"]["201"]["web"] = "ログインIDもしくはパスワードが間違っています";
$msg["ja"]["201"]["log"] = "Invalid loginID or password.";


/* Distribution Reservation Page message */
define("SEND_TEST_ERROR", "300");
$msg["ja"]["300"]["web"] = "テスト配信ができませんでした";
$msg["ja"]["300"]["log"] = "Cannot send test mail.";

define("NOT_SELECT_FROMADDR", "301");
$msg["ja"]["301"]["web"] = "Fromアドレスが選択されていません";
$msg["ja"]["301"]["log"] = "No from address selected.";

define("INVALID_ITEM", "302");
$msg["ja"]["302"]["web"] = "(%s)の書式が不正です(%s)";
$msg["ja"]["302"]["log"] = "Invalid (%s).(%s)";

define("SEND_TEST_FAIL", "303");
$msg["ja"]["303"]["web"] = "テスト配信に失敗しました";
$msg["ja"]["303"]["log"] = "Fail to send test delivery";

define("SEND_IMMEDIATE_FAIL", "304");
$msg["ja"]["304"]["web"] = "即時配信に失敗しました";
$msg["ja"]["304"]["log"] = "Fail to send immediate delivery";

define("RESERVATION_SPECIFIED_FAIL", "305");
$msg["ja"]["305"]["web"] = "時間指定配信の予約に失敗しました";
$msg["ja"]["305"]["log"] = "Failed to reservation of time specified delivery.";

define("NOT_SELECT_TOADDR", "306");
$msg["ja"]["306"]["web"] = "Toアドレスが選択されていません";
$msg["ja"]["306"]["log"] = "No to address selected";

define("INVALID_UPFILE_FORMAT", "307");
$msg["ja"]["307"]["web"] = "アップロードファイルの書式が不正です(%s行目)";
$msg["ja"]["307"]["log"] = "Invalid upload file format.(line: %s)";

define("INVALID_UPFILE_COLUMN", "308");
$msg["ja"]["308"]["web"] = "アップロードファイルのカラム数が不正です(%s行目)";
$msg["ja"]["308"]["log"] = "Number of columns in the upload file is invalid.(line: %s)";

define("INVALID_UPFILE_MAIL", "309");
$msg["ja"]["309"]["web"] = "アップロードファイルのメールアドレスの形式が不正です(%s行目)";
$msg["ja"]["309"]["log"] = "Form of e-mail address of the upload file is invalid.(line: %s)";

define("NOT_UPLOADED", "310");
$msg["ja"]["310"]["web"] = "ファイルがアップロードされていません";
$msg["ja"]["310"]["log"] = "The file is not uploaded.";

define("INVALID_RESERV_DATE", "311");
$msg["ja"]["311"]["web"] = "配信予約日時の値が不正です";
$msg["ja"]["311"]["log"] = "Invalid reservation date form.";

define("PAST_RESERV_DATE", "312");
$msg["ja"]["312"]["web"] = "配信予約日時が過去です";
$msg["ja"]["312"]["log"] = "Reservation date is past.";

define("UPFILE_EMPTY", "313");
$msg["ja"]["313"]["web"] = "アップロードファイルの中身が空です";
$msg["ja"]["313"]["log"] = "Uploaded file is empty.";

define("INVALID_SUBJECT", "314");
$msg["ja"]["314"]["web"] = "件名が不正です";
$msg["ja"]["314"]["log"] = "Invalid subject.";

define("INVALID_MAINTEXT", "315");
$msg["ja"]["315"]["web"] = "メール本文に半角カタカナが含まれています";
$msg["ja"]["315"]["log"] = "Invalid maintext.";

define("RESERV_SPECIFIED_SUCCESS", "316");
$msg["ja"]["316"]["web"] = "時間指定配信の予約に成功しました";
$msg["ja"]["316"]["log"] = "Successful to reservation of time specified delivary.";

define("TEST_DELIV_SUCCESS", "317");
$msg["ja"]["317"]["web"] = "テスト配信に成功しました";
$msg["ja"]["317"]["log"] = "Successful to send test delivery";

define("IMMD_DELIV_SUCCESS", "318");
$msg["ja"]["318"]["web"] = "即時配信に成功しました";
$msg["ja"]["318"]["log"] = "Successful to send immediate delivery";

define("INVALID_EXEC_METHOD", "319");
$msg["ja"]["319"]["web"] = "usage: ./msqualldeliver (-t or -i)";
$msg["ja"]["319"]["log"] = "usage: ./msqualldeliver (-t or -i)";


/* Senders and Receivers Management Page message */
define("READ_SENDER_ERROR", "400");
$msg["ja"]["400"]["web"] = "送受信者管理ファイルが読み込めません";
$msg["ja"]["400"]["log"] = "Cannot read sender list file.(%s)";

define("SENDER_DEL_FAIL", "401");
$msg["ja"]["401"]["web"] = "送受信者の削除に失敗しました";
$msg["ja"]["401"]["log"] = "Cannot delete sender.(%s)";

define("SENDER_MOD_FAIL", "402");
$msg["ja"]["402"]["web"] = "送受信者の変更に失敗しました";
$msg["ja"]["402"]["log"] = "Cannot modify sender.(%s)";

define("SENDER_ADD_FAIL", "403");
$msg["ja"]["403"]["web"] = "送受信者の登録に失敗しました";
$msg["ja"]["403"]["log"] = "Cannot add sender.(%s)";

define("NOT_SELECT_SENDER", "404");
$msg["ja"]["404"]["web"] = "送受信者が選択されていません";
$msg["ja"]["404"]["log"] = "No sender selected.";

define("SENDER_FORM_INVALID", "405");
$msg["ja"]["405"]["web"] = "送受信者管理ファイルの書式が異なっています";
$msg["ja"]["405"]["log"] = "Invalid form.(%s)";

define("NOT_EXIST_SENDER", "406");
$msg["ja"]["406"]["web"] = "選択されたデータは存在しません";
$msg["ja"]["406"]["log"] = "The selected date does not exist.(%s)";

define("LOCK_FILE_FAIL", "407");
$msg["ja"]["407"]["web"] = "ファイルのロックに失敗しました";
$msg["ja"]["407"]["log"] = "Failed to lock the file.(%s)";

define("UNLOCK_FILE_FAIL", "408");
$msg["ja"]["408"]["web"] = "ファイルのロック解除に失敗しました";
$msg["ja"]["408"]["log"] = "Failed to unlock the file.(%s)";

define("RENAME_FILE_FAIL", "409");
$msg["ja"]["409"]["web"] = "ファイル名のリネームに失敗しました";
$msg["ja"]["409"]["log"] = "Failed to rename the filename.(%s)";

define("OPEN_FILE_FAIL", "410");
$msg["ja"]["410"]["web"] = "ファイルのオープンに失敗しました";
$msg["ja"]["410"]["log"] = "Failed to open the file.(%s)";

define("DELETE_FILE_FAIL", "411");
$msg["ja"]["411"]["web"] = "ファイルの削除に失敗しました";
$msg["ja"]["411"]["log"] = "Failed to delete the file.(%s)";

define("CREATE_FILE_FAIL", "412");
$msg["ja"]["412"]["web"] = "ファイルの作成に失敗しました";
$msg["ja"]["412"]["log"] = "Failed to create the file.(%s)";

define("READ_FILE_FAIL", "413");
$msg["ja"]["413"]["web"] = "ファイルの読込に失敗しました";
$msg["ja"]["413"]["log"] = "Failed to read the file.(%s)";

define("ADD_SUCCESS", "414");
$msg["ja"]["414"]["web"] = "登録に成功しました";
$msg["ja"]["414"]["log"] = "Successful to add sender_list.";

define("MODIFY_SUCCESS", "415");
$msg["ja"]["415"]["web"] = "更新に成功しました";
$msg["ja"]["415"]["log"] = "Successful to modify sender_list.";

define("DELETE_SUCCESS", "416");
$msg["ja"]["416"]["web"] = "削除に成功しました";
$msg["ja"]["416"]["log"] = "Successful to delete sender_list.";

define("WRITE_FILE_FAIL", "417");
$msg["ja"]["417"]["web"] = "ファイルの書き込みに失敗しました";
$msg["ja"]["417"]["log"] = "Failed to write the file.(%s)";


/* Delivery Status Confirmation Page message */
define("JOB_DEL_FAIL", "501");
$msg["ja"]["501"]["web"] = "ジョブの削除に失敗しました";
$msg["ja"]["501"]["log"] = "Cannot delete job.(%s)";

define("NOT_JOB_SPECIFIED", "502");
$msg["ja"]["502"]["web"] = "指定された条件のジョブが見つかりません";
$msg["ja"]["502"]["log"] = "Cannot find job specified.(%s)";

define("INVALID_DOMAIN_IP", "503");
$msg["ja"]["503"]["web"] = "ドメインまたはIPアドレスの形式が不正です";
$msg["ja"]["503"]["log"] = "Invalid IP address or domain.(%s : %s)";

define("INVALID_MAIL", "504");
$msg["ja"]["504"]["web"] = "メールアドレスの形式が不正です";
$msg["ja"]["504"]["log"] = "Invalid mail address.(%s : %s)";

define("EXEC_PROGRAM_FAIL", "505");
$msg["ja"]["505"]["web"] = "サブプログラムの実行に失敗しました";
$msg["ja"]["505"]["log"] = "Cannot exec delivery sub program";

define("READ_EVNELOPE_FAIL", "506");
$msg["ja"]["506"]["web"] = "宛先リストが読み込めませんでした";
$msg["ja"]["506"]["log"] = "Cannot read envelope to list.(%s)";

define("INVALID_ENVELOPE_FORMAT", "507");
$msg["ja"]["507"]["web"] = "宛先リストの書式が不正です(%s行目)";
$msg["ja"]["507"]["log"] = "Invalid envelope_to_file format.(line: %s)";

define("DELIVERY_LOG_FAIL", "508");
$msg["ja"]["508"]["web"] = "配信結果に出力ができません";
$msg["ja"]["508"]["log"] = "Cannot write delivery log.(%s)";

define("CONVERTY_MAIL_FAIL", "509");
$msg["ja"]["509"]["web"] = "文字コードの変換に失敗しました";
$msg["ja"]["509"]["log"] = "Cannot convert mail body.(%s)";

define("COMMAND_FAIL", "510");
$msg["ja"]["510"]["web"] = "コマンドの実行に失敗しました";
$msg["ja"]["510"]["log"] = "Sendmail command failed.(%s)";

define("CONTROL_DUPLICATED", "511");
$msg["ja"]["511"]["web"] = "管理番号が重複しました";
$msg["ja"]["511"]["log"] = "Management number was duplicated.(%s)";

define("SEND_FAIL", "512");
$msg["ja"]["512"]["web"] = "メールの送信に失敗しました";
$msg["ja"]["512"]["log"] = "Sendmail command failed.(%s)";

define("INVALID_START_END", "513");
$msg["ja"]["513"]["web"] = "start-end_timeの書式が不正です(%s)";
$msg["ja"]["513"]["log"] = "Invalid start-end_time.(%s)";

define("CREATE_TIME_FAIL", "514");
$msg["ja"]["514"]["web"] = "時間の作成に失敗しました";
$msg["ja"]["514"]["log"] = "Fail to make the time";

define("INVALID_MAILINFO", "515");
$msg["ja"]["515"]["web"] = "mail_infoの書式が不正です(%s)";
$msg["ja"]["515"]["log"] = "Invalid mail_info.(%s)";

define("INVALID_SENDTIME", "516");
$msg["ja"]["516"]["web"] = "sendtimeファイルが不正です(%s)";
$msg["ja"]["516"]["log"] = "Invalid sendtime.(%s)";

define("INVALID_FILE", "517");
$msg["ja"]["517"]["web"] = "ファイルの形式が不正です(%s)";
$msg["ja"]["517"]["log"] = "Invalid file.(%s)";

define("INVALID_DELIVERY_DATE", "518");
$msg["ja"]["518"]["web"] = "検索配信日時の形式が不正です";
$msg["ja"]["518"]["log"] = "Format of the delivery date and time is invalid.";

define("BEFORE_DATE", "519");
$msg["ja"]["519"]["web"] = "検索のエンド年月日がスタート年月日以前です";
$msg["ja"]["519"]["log"] = "End date of the search is a start date before.";

define("DISPLAYED_ERR", "520");
$msg["ja"]["520"]["web"] = "画面が表示できません";
$msg["ja"]["520"]["log"] = "Can not be displayed on the screen.";

define("NO_SUCH_JOB", "521");
$msg["ja"]["521"]["web"] = "指定された条件のジョブが見つかりません";
$msg["ja"]["521"]["log"] = "Job of the conditions specified was not found.";

define("ALREADY_BREAK", "522");
$msg["ja"]["522"]["web"] = "指定されたジョブは既に中断されています";
$msg["ja"]["522"]["log"] = "The specified job has been suspended already.";

define("FAIL_BREAK", "523");
$msg["ja"]["523"]["web"] = "ジョブの中断に失敗しました";
$msg["ja"]["523"]["log"] = "Failed to break the job.";

define("NOT_BREAK", "524");
$msg["ja"]["524"]["web"] = "指定されたジョブは中断されていません";
$msg["ja"]["524"]["log"] = "The specified job does not have interrupted.";

define("FAIL_RESTART", "525");
$msg["ja"]["525"]["web"] = "ジョブの再開に失敗しました";
$msg["ja"]["525"]["log"] = "Failed to resume the job.";

define("NOT_DELETE", "526");
$msg["ja"]["526"]["web"] = "指定されたジョブは削除できません";
$msg["ja"]["526"]["log"] = "Can not delete the specified job.";

define("FAIL_DELETE", "527");
$msg["ja"]["527"]["web"] = "ジョブの削除に失敗しました";
$msg["ja"]["527"]["log"] = "Failed to delete the job.";

define("NOT_SELECT_JOB", "528");
$msg["ja"]["528"]["web"] = "ジョブが選択されていません";
$msg["ja"]["528"]["log"] = "Job is not selected.";

define("NOT_RESTART", "529");
$msg["ja"]["529"]["web"] = "指定されたジョブは再開できません";
$msg["ja"]["529"]["log"] = "Can not return to the specified job.";

define("SUCCESS_BREAK", "530");
$msg["ja"]["530"]["web"] = "指定されたジョブを中断しました";
$msg["ja"]["530"]["log"] = "Sccessful interruption of job.";

define("SUCCESS_RESTART", "531");
$msg["ja"]["531"]["web"] = "指定されたジョブを再開しました";
$msg["ja"]["531"]["log"] = "Succeeded to resume job.";

define("SUCCESS_DELETE", "532");
$msg["ja"]["532"]["web"] = "指定されたジョブを削除しました";
$msg["ja"]["532"]["log"] = "Succeeded to delete job.";

define("CAN_NOT_BREAK", "533");
$msg["ja"]["533"]["web"] = "指定されたジョブは中断できません";
$msg["ja"]["533"]["log"] = "Can not interruption the specified job.";


/* Displayed together message */
define("DISPLAYED_TOGETHER", "800");
$msg["ja"]["800"]["web"] = "%s";
$msg["ja"]["800"]["log"] = "Invalid upload file format.";

/* System Error message */
define("WRITE_ERROR", "900");
$msg["ja"]["900"]["web"] = "システム内部でエラーが発生しました。<br>\n管理者へお問い合わせ下さい。";
$msg["ja"]["900"]["log"] = "Cannot write tmp file.(%s)";

define("READ_ERROR", "901");
$msg["ja"]["901"]["web"] = "システム内部でエラーが発生しました。<br>\n管理者へお問い合わせ下さい。";
$msg["ja"]["901"]["log"] = "Cannot read tmp file.(%s)";

define("TMP_FILE_ERROR", "902");
$msg["ja"]["902"]["web"] = "テンプレートファイルが読み込めません(%s)";
$msg["ja"]["902"]["log"] = "Cannot read tmplate file.(%s)";

?>
