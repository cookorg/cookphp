<?php

namespace app\plugin;

/**
 * 区块链
 */
class BlockChain {

    /**
     * 实例化一个新的区块链。
     */
    public function __construct() {
        $this->chain = [$this->createGenesisBlock()];
        $this->difficulty = 4;
    }

    /**
     * 创建生成块。
     */
    private function createGenesisBlock() {
        return new Block(0, strtotime("2017-01-01"), "Genesis Block");
    }

    /**
     * 获取链中的最后一个块。
     */
    public function getLastBlock() {
        return $this->chain[count($this->chain) - 1];
    }

    /**
     * 将一个新块推到链上
     */
    public function push($block) {
        $block->previousHash = $this->getLastBlock()->hash;
        $this->mine($block);
        array_push($this->chain, $block);
    }

    /**
     * 挖
     */
    public function mine($block) {
        //while (substr($block->hash, 0, $this->difficulty) !== str_repeat("0", $this->difficulty)) {
            $block->nonce= hash("md5",$block->hash.$this->difficulty.'www.cookphp.org');
            $block->hash = $block->calculateHash();
        //}
        //echo "Block mined: " . $block->hash . "\n";
    }

    /**
     * 验证区块链的完整性。 如果区块链有效则返回true，否则返回false
     */
    public function isValid() {
        for ($i = 1; $i < count($this->chain); $i++) {
            $currentBlock = $this->chain[$i];
            $previousBlock = $this->chain[$i - 1];

            if ($currentBlock->hash != $currentBlock->calculateHash()) {
                return false;
            }

            if ($currentBlock->previousHash != $previousBlock->hash) {
                return false;
            }
        }

        return true;
    }

}
