<?php

//责任链模式

// 词汇过滤链条
abstract class FilterChain
{
    /**
     * @var FilterChain
     */
    protected $next;//下一个责任
    public function setNext(FilterChain $next)
    {
        $this->next = $next;
    }
    abstract public function filter($message);
}

// 严禁词汇
class FilterStrict extends FilterChain
{
    public function filter($message)
    {
        foreach (['枪X', '弹X', '毒X'] as $v) {
            if (strpos($message, $v) !== false) {
                var_dump('该信息包含敏感词汇！');
                return false;
            }
        }
        if ($this->next) {
            return $this->next->filter($message);
        } else {
            return $message;
        }
    }
}

// 警告词汇
class FilterWarning extends FilterChain
{
    public function filter($message)
    {
        $message = str_replace(['打架', '丰胸', '偷税'], '*', $message);
        if ($this->next) {
            return $this->next->filter($message);
        } else {
            return $message;
        }
    }
}

// 手机号加星
class FilterMobile extends FilterChain
{
    public function filter($message)
    {
        $message = preg_replace("/(1[3|5|7|8]\d)\d{4}(\d{4})/i", "$1****$2", $message);
        if ($this->next) {
            return $this->next->filter($message);
        } else {
            return $message;
        }
    }
}

$model1 = new FilterStrict();
$model2 = new FilterWarning();
$model3 = new FilterMobile();
$model1->setNext($model2);
$model2->setNext($model3);

$m1 = "现在开始测试链条1：语句中不包含敏感词，需要替换掉打架这种词，然后给手机号加上星：13333333333，这样的数据才可以对外展示哦";
echo $model1->filter($m1);
echo PHP_EOL;

$m2 = "现在开始测试链条2：这条语句走不到后面，因为包含了毒X，直接就报错了！！！语句中不包含敏感词，需要替换掉打架这种词，然后给手机号加上星：13333333333，这样的数据才可以对外展示哦";
echo $model1->filter($m2);
echo PHP_EOL;