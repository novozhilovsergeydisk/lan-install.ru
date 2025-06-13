<?php
class Logger {
    private $logDir;
    private $accessLog;
    private $errorLog;
    
    public function __construct() {
        $this->logDir = __DIR__ . '/../logs/';
        $this->accessLog = $this->logDir . 'access.log';
        $this->errorLog = $this->logDir . 'error.log';
        
        // Create logs directory if not exists
        if (!file_exists($this->logDir)) {
            mkdir($this->logDir, 0755, true);
        }
    }
    
    public function logRequest(Request $request) {
        $logData = [
            'timestamp' => date('Y-m-d H:i:s'),
            'ip' => $request->getIp(),
            'method' => $request->getMethod(),
            'path' => $request->getPath(),
            'user_agent' => $request->getUserAgent(),
            'referer' => $request->getReferer(),
            'headers' => $request->getHeaders(),
        ];
        
        $logMessage = json_encode($logData) . PHP_EOL;
        file_put_contents($this->accessLog, $logMessage, FILE_APPEND);
    }
    
    public function logResponse(Response $response) {
    		$logData = [
        		'timestamp' => date('Y-m-d H:i:s'),
        		'status_code' => $response->getStatusCode(),
        		'headers' => headers_list(), // Получаем реальные отправленные заголовки
        		'content_length' => strlen($response->getContent()),
    		];

    		$logMessage = json_encode($logData) . PHP_EOL;
    		file_put_contents($this->accessLog, $logMessage, FILE_APPEND);
	}
    
    public function logError(Exception $e) {
        $logData = [
            'timestamp' => date('Y-m-d H:i:s'),
            'message' => $e->getMessage(),
            'code' => $e->getCode(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
        ];
        
        $logMessage = json_encode($logData) . PHP_EOL;
        file_put_contents($this->errorLog, $logMessage, FILE_APPEND);
    }
}
