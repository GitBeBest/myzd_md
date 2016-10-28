<?php
namespace app\commands;
use yii\console\Controller;

/**
 * the command for module create and remove
 */
class ModuleController extends Controller
{
	public $templatePath;

	/**
	 * module create
	 * @param $name module name
	 * @return int
	 */
	public function actionCreate($name)
	{
		if(!isset($name))
		{
			echo "Error: module ID is required.\n";
			echo $this->getHelp();
			return 1;
		}

		$moduleID=$name;
		$moduleClass=ucfirst($moduleID).'Module';
		$modulePath=\Yii::$app->getBasePath().DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.$moduleID;

		$sourceDir=$this->templatePath===null?YII2_PATH.'/cli/views/shell/module':$this->templatePath;
		$list=$this->buildFileList($sourceDir,$modulePath);
		$list['module.php']['target']=$modulePath.DIRECTORY_SEPARATOR.$moduleClass.'.php';
		$list['module.php']['callback']=array($this,'generateModuleClass');
		$list['module.php']['params']=array(
				'moduleClass'=>$moduleClass,
				'moduleID'=>$moduleID,
		);
		$list[$moduleClass.'.php']=$list['module.php'];
		unset($list['module.php']);

		$this->copyFiles($list);

		echo <<<EOD
		Module '{$moduleID}' has been created under the following folder:
			$modulePath

		You may access it in the browser using the following URL:
			http://hostname/path/to/index.php?r=$moduleID

		Note, the module needs to be installed first by adding '{$moduleID}'
		to the 'modules' property in the application configuration.

EOD;
		return 0;
	}

	/**
	 * module remove
	 * @param $name
	 */
	public function actionRemove($name)
	{

	}

	/**
	 * help info
	 */
	public function actionHelp()
	{

	}

	protected function buildFileList($sourceDir, $targetDir, $baseDir='', $ignoreFiles=array(), $renameMap=array())
	{
		$list=array();
		$handle=opendir($sourceDir);
		while(($file=readdir($handle))!==false)
		{
			if(in_array($file,array('.','..','.svn','.gitignore')) || in_array($file,$ignoreFiles))
				continue;
			$sourcePath=$sourceDir.DIRECTORY_SEPARATOR.$file;
			$targetPath=$targetDir.DIRECTORY_SEPARATOR.strtr($file,$renameMap);
			$name=$baseDir===''?$file : $baseDir.'/'.$file;
			$list[$name]=array('source'=>$sourcePath, 'target'=>$targetPath);
			if(is_dir($sourcePath))
				$list=array_merge($list,$this->buildFileList($sourcePath,$targetPath,$name,$ignoreFiles,$renameMap));
		}
		closedir($handle);
		return $list;
	}

	protected function copyFiles($fileList,$overwriteAll=false)
	{
		foreach($fileList as $name=>$file)
		{
			$source=strtr($file['source'],'/\\',DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR);
			$target=strtr($file['target'],'/\\',DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR);
			$callback=isset($file['callback']) ? $file['callback'] : null;
			$params=isset($file['params']) ? $file['params'] : null;

			if(is_dir($source))
			{
				$this->ensureDirectory($target);
				continue;
			}

			if($callback!==null)
				$content=call_user_func($callback,$source,$params);
			else
				$content=file_get_contents($source);
			if(is_file($target))
			{
				if($content===file_get_contents($target))
				{
					echo "  unchanged $name\n";
					continue;
				}
				if($overwriteAll)
					echo "  overwrite $name\n";
				else
				{
					echo "      exist $name\n";
					echo "            ...overwrite? [Yes|No|All|Quit] ";
					$answer=trim(fgets(STDIN));
					if(!strncasecmp($answer,'q',1))
						return;
					elseif(!strncasecmp($answer,'y',1))
						echo "  overwrite $name\n";
					elseif(!strncasecmp($answer,'a',1))
					{
						echo "  overwrite $name\n";
						$overwriteAll=true;
					}
					else
					{
						echo "       skip $name\n";
						continue;
					}
				}
			}
			else
			{
				$this->ensureDirectory(dirname($target));
				echo "   generate $name\n";
			}
			file_put_contents($target,$content);
		}
	}

	protected function ensureDirectory($directory)
	{
		if(!is_dir($directory))
		{
			$this->ensureDirectory(dirname($directory));
			echo "      mkdir ".strtr($directory,'\\','/')."\n";
			mkdir($directory);
		}
	}
}