<?php


namespace vekas\RDC;

/**
 * stands for ReturnedDependencyController
 */
class RDC {
    private string $script ;
    /**
     * Summary of deps
     * @var array
     */
    private array $deps = [];
    /**
     * @param string $path real filename path
     */
    function __construct(private string $path){
        $this->script = file_get_contents($path);
    }
    
    /**
     * Summary of addDeps
     * @param array $deps
     * @return static
     */
    function addDeps(array $deps){
        foreach($deps as $dependency) {
            array_push($this->deps,$dependency);
        }
        return $this;
    }

    /**
     * Summary of update
     * @return void
     */
    function update(){
        $oldDeps = $this->getDepsFromString($this->script);
        var_dump($oldDeps);
        $newDeps = [...$oldDeps,...$this->deps];
        $newDeps = $this->convertArrayToDepsDefinition($newDeps);
        $this->script = $this->applyDeps($this->script,$newDeps);
        $this->applyChange();
    }

    /**
     * Summary of getDepsFromString
     * @param string $deps
     * @return mixed
     */
    private function getDepsFromString(string $deps) : array {
        preg_match_all("/([\w\d]*)[\\]*::class[\s]*=>[\s]*new[\s]*[\w\d]*/",$deps,$matches);
        return $matches[1];
    }

    /**
     * return dependencies definition as dep::class => new dep
     */
    function convertArrayToDepsDefinition(array $deps) : string{
        $replacement = "";
        $seperator = ",";
        foreach($deps as $key => $dep) {
            if($key == (count($deps)-1)) {
                $seperator = "";
            }
            $replacement .= $dep."::class => new ". $dep. $seperator . PHP_EOL;
        }

        return $replacement;
    }

    /**
     * Summary of applyDeps
     * @param string $script
     * @param string $deps
     * @return array|string|null
     */
    private function applyDeps(string $script,string $deps){
        return preg_replace("/(return\s*\[)[\W\w]*(\][\W\w]*)/","$1 \n $deps $2",$script);
    }
    /**
     * Summary of applyChange
     * @return void
     */
    private function applyChange() {
        file_put_contents($this->path,$this->script);
    }


    /**
     * Get the value of script
     */
    public function getScript(): string
    {
        return $this->script;
    }
}