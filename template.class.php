<?php

class template {
	//模版引擎原文件的所在目录
	private $templateDir;
    //文件编译后的存放目录
	private $compileDir;
    //我们在模版文件中需要替换掉的文件他们需要一个标记让模版引擎去识别我们要替换的是哪块内容 左标记
	private $leftTag = '{#';
    //右标记
	private $rightTag = '#}';
    //用来存储当前正在编译的模版文件名
	private $currentTemp = '';
    //用来存储当前正在编译的模版文件中的html代码 也就是我们会从原文件中读取一段html代码把它存放里
    //通过一系列的正则替换将这段代码写入到一个目标文件就是编译之后的模版文件
	private $outputHtml;
    //变量池 在编译模版原文件之前呢我们会把模版中需要用到的变量 把他们的值统统放在变量池中
    //当模版文件被编译之后就可以从变量池中通过标记获得变量的值
	private $varPool = array();
	
	public function __construct($templateDir, $compileDir, $leftTag = null, $rightTag = null) {
		$this->templateDir = $templateDir;
		$this->compileDir = $compileDir;
		if(!empty($leftTag)) $this->leftTag = $leftTag;
		if(!empty($rightTag)) $this->rightTag = $rightTag;
	}
	
	public function assign($tag, $var) {
		$this->varPool[$tag] = $var;
	}
	
	public function getVar($tag) {
		return $this->varPool[$tag];
	}
	
	public function getSourceTemplate($templateName, $ext = '.html') {
		$this->currentTemp = $templateName;
		$sourceFilename = $this->templateDir.$this->currentTemp.$ext;
		$this->outputHtml = file_get_contents($sourceFilename);
	}
	
	public function compileTemplate($templateName = null, $ext = '.html') {
		$templateName = empty($templateName) ? $this->currentTemp : $templateName;
		
		$pattern = '/'.preg_quote($this->leftTag);
		$pattern .= ' *\$([a-zA-Z_]\w*) *';
		$pattern .= preg_quote($this->rightTag).'/';
		$this->outputHtml = preg_replace($pattern, '<?php echo $this->getVar(\'$1\');?>', $this->outputHtml);

		$compiledFilename = $this->compileDir.md5($templateName).$ext;
		file_put_contents($compiledFilename, $this->outputHtml);
	}
	
	public function display($templateName = null, $ext = '.html') {
		$templateName = empty($templateName) ? $this->currentTemp : $templateName;
		include_once $this->compileDir.md5($templateName).$ext;
	}
	
}