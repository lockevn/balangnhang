<?php

define("_UPGRADER_TITLE", "Docebo 3.6.0.3 - 更新版");
define("_JUMP_TO_CONTENT", "跳转到内容");
define("_CHOOSE_LANG", "选择语言");
define("_LANG_SELECTION", "选择语言");

// choose begin
define("_TITLE_1OF2", "步骤2中之1  : 选择开始版本");
define("_IS_PRESENT_DIRECTORIES","这儿已经有结构目录不再使用，我门建议删除它: ");
define("_LACKING_DIRECTORIES","一些目录已经丢失，没有它们您不能正确使用系统 : ");
define("_CANT_CONNECT_WITH_DB", "数据库连接失败，请在 config.php 中检查正确的参数");
define("_CANT_CONNECT_WITH_FTP","FTP 连接失败，请在 config.php 中检查正确的参数");
define("_CHECKED_DIRECTORIES","一些有关文件存储的目录不存在或者没有正确的写许可");
define("_EMPTY_DIRECTORIES","一些有关文件存储的目录为空，您确定您已经没有任何的旧文件需要导入了吗?");
define("_START_VERSION","开始版本");
define("_END_VERSION","最终版本");
define("_DOUPGRADE", "升级过程中");

// result
define("_TITLE_2OF2","步骤2中之2 : 系统更新");
define("_UPGRADING_VERSION","更新版本 : ");
define("_FAILED_OPERATION","操作失败，错误代码 : ");
define("_SUCCESSFULL_OPERATION", "操作成功对于: ");
define("_CRITICAL_ERROR_UPGRADE_SUSPENDED","更新出现严重错误，更新已经被停止");
define("_TITLE_STEP3", "语言更新");
define("_LANG_INSTALLED", "更新语言");
define("_LANGUAGE", "");
define("_PLATFORM", " 对于平台");
define("_LANGUAGE_NOT_FOUND", "语言文件未发现");
define("_NEXT", "下一步");
define("_NEXTSTEP", "下一步");
define("_ENDSTEP", "结束");
define("_END_PHRASE", "更新已经成功完成");
define("_CRITICAL_ERROR","严重错误 ");
define("_NOTSCORM","这台服务器不支持 domxml 或者它不是 php5，您不能使用 docebo，请要求您的提供商在这台服务器上安装 domxml 扩展");
define("_YOU_DONT_HAVE_FUNCTION_OVERLOAD","超负载功能不是活动的，这意味着您必须有一个 php 4.3.0 版本或更高。 Linux mandriva 已在不包括超负载的条件下编译，搜索一个包包括名字类似于: php4-overload-xxxxx.mdk 并且安装模版， Linux fedora core 4 有时会在超负载条件下出现错误， <a href=\"http://download.fedora.redhat.com/pub/fedora/linux/core/updates/4/\" target=\"_blank\">请修补它</a>。 假如您使用 windows 的机器，我们建议您使用 <a href=\"http://www.easyphp.org\" target=\"_blank\">easyphp 1.8</a>.");
define("_NEXT_OVERWRITELANG", "标准语言更新进行中 (将覆盖所有)");
define("_NEXT_ONLY_ADD", "语言更新进行中，这将仅添加新词，而不覆盖旧词");
define("_CONVERT_TO_UTF", "utf-8 转换过程进行中 ...");
define("_CONVERT_TO_UTF_COMMENT", "我们正在更新语言和内容到 utf-8 格式，不要停止这个操作");
?>