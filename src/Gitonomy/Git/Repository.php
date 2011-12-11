<?php

namespace Gitonomy\Git;

/**
 * Git repository object.
 *
 * Main entry point for browsing a Git repository.
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class Repository
{
    /**
     * Path to the repository
     *
     * @var string
     */
    protected $path;

    /**
     * Cache containing all objets of the repository.
     *
     * Associative array, indexed by object hash
     *
     * @var array
     */
    protected $objects;

    /**
     * Constructor.
     *
     * @param string $path Path to the Git repository
     *
     * @throws InvalidArgumentException The folder does not exists
     */
    public function __construct($path)
    {
        $this->objects = array();

        if (!is_dir($path)) {
            throw new \InvalidArgumentException(sprintf('The folder "%s" does not exists'));
        }
        $this->path   = $path;
    }

    /**
     * Returns the path to the Git repository.
     *
     * @return string A directory path
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Instanciates a revision.
     *
     * @param string $name Name of the revision
     *
     * @return Gitonomy\Git\Revision
     */
    public function getRevision($name)
    {
        return new Revision($this, $name);
    }

    /**
     * Instanciates a commit object or fetches one from the cache.
     *
     * @param string $hash A commit hash, with a length of 40
     *
     * @return Gitonomy\Git\Commit
     */
    public function getCommit($hash)
    {
        if (! isset($this->objects[$hash])) {
            $this->objects[$hash] = new Commit($this, $hash);
        }

        return $this->objects[$hash];
    }

    /**
     * Executes a shell command on the repository, using PHP pipes.
     *
     * @param string $command The command to execute
     */
    public function shell($command)
    {
        $argument = sprintf('%s \'%s\'', $command, $this->path);

        proc_open('git shell -c '.escapeshellarg($argument), array(STDIN, STDOUT, STDERR), $pipes);
    }
}