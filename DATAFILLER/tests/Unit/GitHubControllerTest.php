<?php

use PHPUnit\Framework\TestCase;
use App\Controllers\GitHubController;

require_once __DIR__ . '/../../controllers/GitHubController.php';

class GitHubControllerTest extends TestCase
{
    private $controller;

    protected function setUp(): void
    {
        $this->controller = new GitHubController();
    }

    public function testGetUserRepositoriesWithValidUser()
    {
        // Test básico para verificar que el método existe y retorna la estructura esperada
        $result = $this->controller->getUserRepositories('testuser');
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        
        // Como es una API externa, solo verificamos la estructura
        if ($result['success']) {
            $this->assertArrayHasKey('data', $result);
        } else {
            $this->assertArrayHasKey('error', $result);
        }
    }

    public function testGetUserRepositoriesWithEmptyUser()
    {
        $result = $this->controller->getUserRepositories('');
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('error', $result);
    }

    public function testGetUserRepositoriesWithInvalidUser()
    {
        // Usar un nombre de usuario que probablemente no exista
        $result = $this->controller->getUserRepositories('this-user-should-not-exist-123456789');
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        
        if (!$result['success']) {
            $this->assertArrayHasKey('error', $result);
        }
    }

    public function testGetUserRepositoriesWithPagination()
    {
        $result = $this->controller->getUserRepositories('testuser', 1, 10);
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
    }
}