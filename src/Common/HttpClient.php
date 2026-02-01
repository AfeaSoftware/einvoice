<?php

declare(strict_types=1);

namespace Afea\Einvoice\Common;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;

class HttpClient
{
    protected GuzzleClient $client;
    protected string $baseUrl;
    protected ?string $token = null;

    public function __construct(string $baseUrl, ?string $token = null)
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->token = $token;
        $this->client = new GuzzleClient([
            'base_uri' => $this->baseUrl,
            'timeout' => 30.0,
            'http_errors' => true,
        ]);
    }

    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * @param array<string, mixed> $query
     * @param array<string, string> $headers
     * @return array<string, mixed>|string
     * @throws HttpClientException
     */
    public function get(string $endpoint, array $query = [], array $headers = [])
    {
        return $this->request('GET', $endpoint, [
            'query' => $query,
            'headers' => $this->mergeHeaders($headers),
        ]);
    }

    /**
     * @param array<string, mixed> $data
     * @param array<string, string> $headers
     * @return array<string, mixed>|string
     * @throws HttpClientException
     */
    public function post(string $endpoint, array $data = [], array $headers = [])
    {
        return $this->request('POST', $endpoint, [
            'json' => $data,
            'headers' => $this->mergeHeaders($headers),
        ]);
    }

    /**
     * @param array<int, array<string, mixed>> $multipart Multipart form data (e.g., ['name' => 'file', 'contents' => fopen(...), 'filename' => 'file.xml'])
     * @param array<string, string> $headers
     * @param array<string, mixed> $query Query parameters
     * @return array<string, mixed>|string
     * @throws HttpClientException
     */
    public function postMultipart(string $endpoint, array $multipart = [], array $headers = [], array $query = [])
    {
        $defaultHeaders = [
            'Accept' => 'application/json',
        ];

        if ($this->token !== null) {
            $defaultHeaders['Authorization'] = 'Bearer ' . $this->token;
        }

        $options = [
            'multipart' => $multipart,
            'headers' => array_merge($defaultHeaders, $headers),
        ];

        if (!empty($query)) {
            $options['query'] = $query;
        }

        return $this->request('POST', $endpoint, $options);
    }

    /**
     * @param array<string, mixed> $data
     * @param array<string, string> $headers
     * @return array<string, mixed>|string
     * @throws HttpClientException
     */
    public function put(string $endpoint, array $data = [], array $headers = [])
    {
        return $this->request('PUT', $endpoint, [
            'json' => $data,
            'headers' => $this->mergeHeaders($headers),
        ]);
    }

    /**
     * @param array<string, string> $headers
     * @return array<string, mixed>
     * @throws HttpClientException
     */
    public function delete(string $endpoint, array $headers = []): array
    {
        return $this->request('DELETE', $endpoint, [
            'headers' => $this->mergeHeaders($headers),
        ]);
    }

    /**
     * Download HTML content with response headers
     *
     * @param string $endpoint
     * @param array<string, mixed> $query
     * @param array<string, string> $headers
     * @return array{content: string, headers: array<string, string>}
     * @throws HttpClientException
     */
    public function downloadHtml(string $endpoint, array $query = [], array $headers = []): array
    {
        $defaultHeaders = [
            'Accept' => 'text/html',
        ];

        if ($this->token !== null) {
            $defaultHeaders['Authorization'] = 'Bearer ' . $this->token;
        }

        try {
            $response = $this->client->request('GET', $endpoint, [
                'query' => $query,
                'headers' => array_merge($defaultHeaders, $headers),
            ]);

            $content = $response->getBody()->getContents();
            
            // Extract relevant headers
            $responseHeaders = [];
            foreach ($response->getHeaders() as $name => $values) {
                $responseHeaders[strtolower($name)] = implode(', ', $values);
            }

            return [
                'content' => $content,
                'headers' => $responseHeaders,
            ];
        } catch (RequestException $e) {
            throw $this->createException($e);
        } catch (GuzzleException $e) {
            throw new HttpClientException(
                'HTTP request failed: ' . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * Download binary file (PDF, etc.)
     *
     * @param string $endpoint
     * @param array<string, mixed> $query
     * @param array<string, string> $headers
     * @return string Binary content
     * @throws HttpClientException
     */
    public function downloadBinary(string $endpoint, array $query = [], array $headers = []): string
    {
        $defaultHeaders = [
            'Accept' => '*/*',
        ];

        if ($this->token !== null) {
            $defaultHeaders['Authorization'] = 'Bearer ' . $this->token;
        }

        try {
            $response = $this->client->request('GET', $endpoint, [
                'query' => $query,
                'headers' => array_merge($defaultHeaders, $headers),
            ]);

            $contentType = $response->getHeaderLine('Content-Type');
            $body = $response->getBody()->getContents();

            // Check if response is JSON (Nilvera returns base64 encoded PDF in JSON)
            if (strpos($contentType, 'application/json') !== false || 
                strpos($contentType, 'text/json') !== false ||
                strpos($contentType, 'text/plain') !== false) {
                
                // Try to parse as JSON
                $decoded = json_decode($body, true);
                
                if (is_array($decoded)) {
                    // Check common fields for base64 PDF
                    $base64Content = $decoded['pdf'] ?? $decoded['content'] ?? $decoded['data'] ?? $decoded['file'] ?? null;
                    if ($base64Content && is_string($base64Content)) {
                        $decodedContent = base64_decode($base64Content, true);
                        if ($decodedContent !== false) {
                            return $decodedContent;
                        }
                    }
                } elseif (is_string($body)) {
                    // If body is a JSON string (quoted string), try to decode it
                    $trimmed = trim($body, '"');
                    if ($trimmed !== $body) {
                        // It was a quoted string, try base64 decode
                        $decodedContent = base64_decode($trimmed, true);
                        if ($decodedContent !== false) {
                            return $decodedContent;
                        }
                    }
                }
            }

            // Return raw binary content (for NES and other binary responses)
            return $body;
        } catch (RequestException $e) {
            throw $this->createException($e);
        } catch (GuzzleException $e) {
            throw new HttpClientException(
                'HTTP request failed: ' . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * @param array<string, mixed> $options
     * @return array<string, mixed>|string
     * @throws HttpClientException
     */
    protected function request(string $method, string $endpoint, array $options = [])
    {
        try {
            $response = $this->client->request($method, $endpoint, $options);
            return $this->parseResponse($response);
        } catch (RequestException $e) {
            throw $this->createException($e);
        } catch (GuzzleException $e) {
            throw new HttpClientException(
                'HTTP request failed: ' . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * @return array<string, mixed>|string
     */
    protected function parseResponse(ResponseInterface $response)
    {
        $body = $response->getBody()->getContents();
        $contentType = $response->getHeaderLine('Content-Type');
        
        // If body is empty, return empty array
        if (empty(trim($body))) {
            return [];
        }
        
        // If content type is text/plain or text/html, return as string
        if (strpos($contentType, 'text/') === 0) {
            return $body;
        }
        
        // Try to parse as JSON
        $data = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            // If JSON parsing fails but body is not empty, return as string
            if (!empty($body)) {
                return $body;
            }
            
            // If body is empty or whitespace, return empty array instead of throwing
            return [];
        }

        return $data ?? [];
    }

    /**
     * @param array<string, string> $headers
     * @return array<string, string>
     */
    protected function mergeHeaders(array $headers): array
    {
        $defaultHeaders = [
            'Accept' => 'application/json',
        ];

        // Don't set Content-Type for multipart requests
        if (!isset($headers['Content-Type'])) {
            $defaultHeaders['Content-Type'] = 'application/json';
        }

        if ($this->token !== null) {
            $defaultHeaders['Authorization'] = 'Bearer ' . $this->token;
        }

        return array_merge($defaultHeaders, $headers);
    }

    protected function createException(RequestException $e): HttpClientException
    {
        $response = $e->getResponse();
        if ($response === null) {
            return new HttpClientException(
                'HTTP request failed: ' . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }

        $statusCode = $response->getStatusCode();
        
        // Read response body once and rewind stream
        $body = $response->getBody()->getContents();
        if ($response->getBody()->isSeekable()) {
            $response->getBody()->rewind();
        }
        
        $errorData = null;
        $message = sprintf('HTTP %d Error', $statusCode);

        try {
            $decoded = json_decode($body, true);
            if (is_array($decoded)) {
                $errorData = $decoded;
                
                // Build detailed message - support both 'message' (NES) and 'Message' (Nilvera)
                $baseMessage = $decoded['message'] ?? $decoded['Message'] ?? sprintf('HTTP %d Error', $statusCode);
                $message = sprintf('HTTP %d: %s', $statusCode, $baseMessage);
                
                // Add error details to message - support both 'errors' (NES) and 'Errors' (Nilvera)
                $errorsArray = $decoded['errors'] ?? $decoded['Errors'] ?? null;
                if (is_array($errorsArray)) {
                    $errors = [];
                    foreach ($errorsArray as $error) {
                        $errorStr = '';
                        
                        // Code - support both 'code' (NES) and 'Code' (Nilvera)
                        $code = $error['code'] ?? $error['Code'] ?? null;
                        if ($code !== null) {
                            $errorStr .= "[{$code}] ";
                        }
                        
                        // Description - support both 'description' (NES) and 'Description' (Nilvera)
                        $description = $error['description'] ?? $error['Description'] ?? null;
                        if ($description !== null) {
                            $errorStr .= $description;
                        }
                        
                        // Detail - support both 'detail' (NES) and 'Detail' (Nilvera)
                        $detail = $error['detail'] ?? $error['Detail'] ?? null;
                        if ($detail !== null && $detail !== '') {
                            $errorStr .= " - {$detail}";
                        }
                        
                        if ($errorStr) {
                            $errors[] = $errorStr;
                        }
                    }
                    if (!empty($errors)) {
                        $message .= "\n\nError Details:\n" . implode("\n", $errors);
                    }
                }
                
                // Nilvera formatı için alternatif error field'ı
                if (isset($decoded['Error']) && is_string($decoded['Error'])) {
                    $message .= "\n\nError: " . $decoded['Error'];
                }
                
                // Add invalid fields to message
                if (isset($decoded['invalidFields']) && is_array($decoded['invalidFields'])) {
                    $fields = [];
                    foreach ($decoded['invalidFields'] as $field) {
                        $fieldStr = '';
                        if (isset($field['field'])) {
                            $fieldStr .= "Field: {$field['field']}";
                        }
                        if (isset($field['description'])) {
                            $fieldStr .= " - {$field['description']}";
                        }
                        if (isset($field['detail']) && $field['detail'] !== '') {
                            $fieldStr .= " ({$field['detail']})";
                        }
                        if ($fieldStr) {
                            $fields[] = $fieldStr;
                        }
                    }
                    if (!empty($fields)) {
                        $message .= "\n\nInvalid Fields:\n" . implode("\n", $fields);
                    }
                }
            } else {
                // If not JSON, use body as message
                $message = sprintf('HTTP %d: %s', $statusCode, $body ?: $e->getMessage());
            }
        } catch (\Exception $ex) {
            // If JSON parsing fails, use body as message
            $message = sprintf('HTTP %d: %s', $statusCode, $body ?: $e->getMessage());
        }

        return new HttpClientException(
            $message,
            $statusCode,
            $e,
            $errorData,
            $body
        );
    }
}
