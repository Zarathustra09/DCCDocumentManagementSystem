<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view documents')->only(['index', 'show']);
        $this->middleware('permission:create documents')->only(['create', 'store']);
        $this->middleware('permission:edit documents')->only(['edit', 'update']);
        $this->middleware('permission:delete documents')->only('destroy');
        $this->middleware('permission:download documents')->only('download');
    }

    public function index(Request $request)
    {
        $query = Document::where('user_id', Auth::id());

        if ($request->has('folder_id')) {
            $query->where('folder_id', $request->folder_id);
        }

        $documents = $query->latest()->paginate(10);
        $folders = Folder::where('user_id', Auth::id())->get();

        return view('document.index', compact('documents', 'folders'));
    }

    public function create()
    {
        $folders = Folder::where('user_id', Auth::id())->get();
        return view('document.create', compact('folders'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240',
            'folder_id' => 'nullable|exists:folders,id',
            'description' => 'nullable|string|max:500',
        ]);

        $file = $request->file('file');
        $originalFilename = $file->getClientOriginalName();
        $filename = time() . '_' . Str::slug(pathinfo($originalFilename, PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('documents', $filename, 'public');

        $document = Document::create([
            'user_id' => Auth::id(),
            'folder_id' => $request->folder_id,
            'filename' => $filename,
            'original_filename' => $originalFilename,
            'file_path' => $path,
            'file_type' => $file->getClientOriginalExtension(),
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'description' => $request->description,
            'meta_data' => [
                'uploaded_at' => now()->toDateTimeString(),
                'ip' => $request->ip(),
            ],
        ]);

        // Redirect to the folder if it exists, otherwise to documents index
        if ($request->folder_id) {
            return redirect()->route('folders.show', $request->folder_id)
                ->with('success', 'Document uploaded successfully');
        }

        return redirect()->route('documents.index')->with('success', 'Document uploaded successfully');
    }

    public function show(Document $document)
    {
        \Log::info('Document show accessed', [
            'document_id' => $document->id,
            'user_id' => Auth::id(),
            'filename' => $document->original_filename,
            'file_type' => $document->file_type
        ]);

        // Check if the user owns this document or has admin role
        if ($document->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            \Log::warning('Unauthorized document access attempt', [
                'document_id' => $document->id,
                'document_owner' => $document->user_id,
                'accessing_user' => Auth::id(),
                'filename' => $document->original_filename
            ]);
            abort(403);
        }

        \Log::info('Document show successful', [
            'document_id' => $document->id,
            'user_id' => Auth::id()
        ]);

        return view('document.show', compact('document'));
    }

    public function edit(Document $document)
    {
        // Check if the user owns this document or has admin role
        if ($document->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403);
        }

        $folders = Folder::where('user_id', Auth::id())->get();
        return view('document.edit', compact('document', 'folders'));
    }

    public function update(Request $request, Document $document)
    {
        // Check if the user owns this document or has admin role
        if ($document->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403);
        }

        $request->validate([
            'folder_id' => 'nullable|exists:folders,id',
            'description' => 'nullable|string|max:500',
        ]);

        $document->update([
            'folder_id' => $request->folder_id,
            'description' => $request->description,
        ]);

        if ($document->folder_id) {
            return redirect()->route('folders.show', $document->folder_id)
                ->with('success', 'Document updated successfully');
        }

        return redirect()->route('documents.index')->with('success', 'Document updated successfully');
    }

    public function destroy(Document $document)
    {
        // Check if the user owns this document or has admin role
        if ($document->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403);
        }

        // Delete the actual file
        Storage::disk('public')->delete($document->file_path);

        // Delete the database record
        $document->delete();


        if ($document->folder_id) {
            return redirect()->route('folders.show', $document->folder_id)
                ->with('success', 'Document deleted successfully');
        }

        return redirect()->route('documents.index')->with('success', 'Document deleted successfully');
    }

    public function download(Document $document)
    {
        // Check if the user owns this document or has admin role
        if ($document->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403);
        }

        return Storage::disk('public')->download(
            $document->file_path,
            $document->original_filename
        );
    }

    public function preview(Document $document)
    {
        \Log::info('Document preview requested', [
            'document_id' => $document->id,
            'user_id' => Auth::id(),
            'filename' => $document->original_filename,
            'file_type' => $document->file_type,
            'file_size' => $document->file_size
        ]);

        // Check if the user owns this document or has admin role
        if ($document->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            \Log::warning('Unauthorized document preview attempt', [
                'document_id' => $document->id,
                'document_owner' => $document->user_id,
                'accessing_user' => Auth::id(),
                'filename' => $document->original_filename
            ]);
            abort(403);
        }

        // Check if it's a Word document
        if (!in_array($document->file_type, ['doc', 'docx'])) {
            \Log::info('Preview not available for file type', [
                'document_id' => $document->id,
                'file_type' => $document->file_type,
                'user_id' => Auth::id()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Preview not available for this file type'
            ], 400);
        }

        try {
            $filePath = storage_path('app/public/' . $document->file_path);

            \Log::info('Processing document preview', [
                'document_id' => $document->id,
                'file_path' => $filePath,
                'file_exists' => file_exists($filePath),
                'user_id' => Auth::id()
            ]);

            if (!file_exists($filePath)) {
                \Log::error('Document file not found', [
                    'document_id' => $document->id,
                    'file_path' => $filePath,
                    'user_id' => Auth::id()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'File not found'
                ], 404);
            }

            // Validate file size (limit to 10MB for preview)
            $fileSize = filesize($filePath);
            if ($fileSize > 10 * 1024 * 1024) {
                \Log::warning('Document too large for preview', [
                    'document_id' => $document->id,
                    'file_size' => $fileSize,
                    'user_id' => Auth::id()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'File too large for preview. Please download to view.'
                ], 400);
            }

            // Validate file is actually a Word document by checking mime type
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $filePath);
            finfo_close($finfo);

            \Log::info('Document mime type validation', [
                'document_id' => $document->id,
                'detected_mime_type' => $mimeType,
                'stored_mime_type' => $document->mime_type,
                'user_id' => Auth::id()
            ]);

            $validMimeTypes = [
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // .docx
                'application/msword', // .doc
                'application/zip', // Sometimes .docx appears as zip
            ];

            if (!in_array($mimeType, $validMimeTypes)) {
                \Log::warning('Invalid Word document format', [
                    'document_id' => $document->id,
                    'detected_mime_type' => $mimeType,
                    'user_id' => Auth::id()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid Word document format'
                ], 400);
            }

            // Try to load the document with better error handling
            try {
                \Log::info('Loading Word document with PHPWord', [
                    'document_id' => $document->id,
                    'user_id' => Auth::id()
                ]);
                $phpWord = \PhpOffice\PhpWord\IOFactory::load($filePath);
            } catch (\PhpOffice\PhpWord\Exception\Exception $e) {
                \Log::error('PHPWord document loading failed', [
                    'document_id' => $document->id,
                    'error' => $e->getMessage(),
                    'user_id' => Auth::id()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Document appears to be corrupted or uses an unsupported format'
                ], 400);
            } catch (\Exception $e) {
                \Log::error('General document loading error', [
                    'document_id' => $document->id,
                    'error' => $e->getMessage(),
                    'user_id' => Auth::id()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to read document: ' . $e->getMessage()
                ], 500);
            }

            // Convert to HTML with error handling
            try {
                \Log::info('Converting document to HTML', [
                    'document_id' => $document->id,
                    'user_id' => Auth::id()
                ]);

                $htmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'HTML');

                // Use temporary file instead of output buffer
                $tempFile = tempnam(sys_get_temp_dir(), 'phpword_preview_');
                $htmlWriter->save($tempFile);

                $htmlContent = file_get_contents($tempFile);
                unlink($tempFile); // Clean up temp file

                \Log::info('Document conversion completed', [
                    'document_id' => $document->id,
                    'html_length' => strlen($htmlContent),
                    'temp_file' => $tempFile,
                    'user_id' => Auth::id()
                ]);

                if (empty($htmlContent)) {
                    \Log::warning('Document conversion resulted in empty content', [
                        'document_id' => $document->id,
                        'user_id' => Auth::id()
                    ]);
                    return response()->json([
                        'success' => false,
                        'message' => 'Document appears to be empty or could not be converted'
                    ], 400);
                }

                // Clean up the HTML content
                $htmlContent = $this->cleanWordHtml($htmlContent);

                \Log::info('Document preview generated successfully', [
                    'document_id' => $document->id,
                    'final_html_length' => strlen($htmlContent),
                    'user_id' => Auth::id()
                ]);

                return response()->json([
                    'success' => true,
                    'content' => $htmlContent
                ]);

            } catch (\Exception $e) {
                \Log::error('Document HTML conversion failed', [
                    'document_id' => $document->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'user_id' => Auth::id()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Error converting document to preview format'
                ], 500);
            }

        } catch (\Exception $e) {
            \Log::error('Document preview error', [
                'document_id' => $document->id,
                'file_path' => $filePath ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred while loading the preview'
            ], 500);
        }
    }

    private function cleanWordHtml($html)
    {
        // Remove XML declaration and DOCTYPE
        $html = preg_replace('/<\?xml[^>]*>/', '', $html);
        $html = preg_replace('/<!DOCTYPE[^>]*>/', '', $html);

        // Replace html tags with div container
        $html = preg_replace('/<html[^>]*>/', '<div class="word-document">', $html);
        $html = str_replace('</html>', '</div>', $html);

        // Remove head section entirely
        $html = preg_replace('/<head[^>]*>.*?<\/head>/s', '', $html);

        // Clean body tags
        $html = preg_replace('/<body[^>]*>/', '', $html);
        $html = str_replace('</body>', '', $html);

        // Remove problematic inline styles but keep basic formatting
        $html = preg_replace('/style="[^"]*?(font-family|color|text-align|font-weight|font-style)[^"]*?"/', '', $html);

        // Clean up multiple spaces and line breaks
        $html = preg_replace('/\s+/', ' ', $html);
        $html = str_replace('> <', '><', $html);

        // Ensure proper paragraph spacing
        $html = str_replace('<p></p>', '', $html);

        return trim($html);
    }

    public function updateContent(Request $request, Document $document)
    {
        \Log::info('Document content update requested', [
            'document_id' => $document->id,
            'user_id' => Auth::id(),
            'content_length' => strlen($request->content)
        ]);

        // Check if the user owns this document or has admin role
        if ($document->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            \Log::warning('Unauthorized document content update attempt', [
                'document_id' => $document->id,
                'document_owner' => $document->user_id,
                'accessing_user' => Auth::id()
            ]);
            abort(403);
        }

        // Validate request
        $request->validate([
            'content' => 'required|string'
        ]);

        $filePath = storage_path('app/public/' . $document->file_path);
        $backupPath = null;

        try {
            if (!file_exists($filePath)) {
                throw new \Exception('Original document file not found');
            }

            // Create backup of original file BEFORE any modifications
            $backupPath = $filePath . '.backup.' . time();
            if (!copy($filePath, $backupPath)) {
                throw new \Exception('Failed to create backup file');
            }

            \Log::info('Backup created successfully', ['backup_path' => $backupPath]);

            // Use a different approach that preserves more of the original document
            $this->updateWordDocumentContent($filePath, $request->content);

            \Log::info('Document content updated successfully', [
                'document_id' => $document->id,
                'user_id' => Auth::id()
            ]);

            // Update file size in database
            $newSize = filesize($filePath);
            $document->update([
                'file_size' => $newSize,
                'updated_at' => now()
            ]);

            // Clean up old backups (keep only last 3)
            $this->cleanupBackups($filePath);

            return response()->json([
                'success' => true,
                'message' => 'Document content updated successfully',
                'file_size' => $newSize
            ]);

        } catch (\Exception $e) {
            \Log::error('Document content update failed', [
                'document_id' => $document->id,
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
                'user_id' => Auth::id()
            ]);

            // Restore from backup if it exists and the original file is corrupted
            if ($backupPath && file_exists($backupPath)) {
                if (!file_exists($filePath) || filesize($filePath) === 0) {
                    copy($backupPath, $filePath);
                    \Log::info('Original file restored from backup', [
                        'document_id' => $document->id,
                        'backup_path' => $backupPath
                    ]);
                }
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to update document: ' . $e->getMessage()
            ], 500);
        }
    }

    private function updateWordDocumentContent($filePath, $htmlContent)
    {
        // Create a temporary file for the new content
        $tempPath = $filePath . '.temp.' . time();

        try {
            // Load the original document to preserve its structure
            $originalPhpWord = \PhpOffice\PhpWord\IOFactory::load($filePath);

            // Create a new document that will contain our updated content
            $newPhpWord = new \PhpOffice\PhpWord\PhpWord();

            // Copy document properties from original
            $docProperties = $originalPhpWord->getDocInfo();
            $newPhpWord->getDocInfo()
                ->setCreator($docProperties->getCreator())
                ->setCompany($docProperties->getCompany())
                ->setTitle($docProperties->getTitle())
                ->setDescription($docProperties->getDescription())
                ->setCategory($docProperties->getCategory())
                ->setLastModifiedBy($docProperties->getLastModifiedBy())
                ->setCreated($docProperties->getCreated())
                ->setModified(time())
                ->setSubject($docProperties->getSubject())
                ->setKeywords($docProperties->getKeywords());

            // Add a section to the new document
            $section = $newPhpWord->addSection([
                'marginLeft' => 720,
                'marginRight' => 720,
                'marginTop' => 720,
                'marginBottom' => 720,
                'headerHeight' => 720,
                'footerHeight' => 720
            ]);

            // Convert HTML content to Word elements
            $this->convertHtmlToWordElements($section, $htmlContent);

            // Write the new document to temp file first
            $writer = \PhpOffice\PhpWord\IOFactory::createWriter($newPhpWord, 'Word2007');
            $writer->save($tempPath);

            // If temp file was created successfully, replace the original
            if (file_exists($tempPath) && filesize($tempPath) > 0) {
                if (!rename($tempPath, $filePath)) {
                    throw new \Exception('Failed to replace original file with updated content');
                }
            } else {
                throw new \Exception('Failed to create temporary updated file');
            }

        } catch (\Exception $e) {
            // Clean up temp file if it exists
            if (file_exists($tempPath)) {
                unlink($tempPath);
            }
            throw $e;
        }
    }

    private function convertHtmlToWordElements($section, $htmlContent)
    {
        // Clean and prepare HTML
        $cleanHtml = $this->prepareHtmlForWord($htmlContent);

        // Use DOMDocument to parse HTML properly
        $dom = new \DOMDocument('1.0', 'UTF-8');
        libxml_use_internal_errors(true);

        // Wrap content in body tag to ensure valid HTML structure
        $wrappedHtml = '<!DOCTYPE html><html><body>' . $cleanHtml . '</body></html>';

        if ($dom->loadHTML($wrappedHtml, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD)) {
            $body = $dom->getElementsByTagName('body')->item(0);
            if ($body) {
                $this->processHtmlNodes($section, $body->childNodes);
            }
        } else {
            // Fallback: treat as plain text if HTML parsing fails
            $section->addText(strip_tags($htmlContent));
        }

        libxml_clear_errors();
    }

    private function prepareHtmlForWord($html)
    {
        // Remove problematic elements and attributes
        $html = preg_replace('/<script[^>]*>.*?<\/script>/is', '', $html);
        $html = preg_replace('/<style[^>]*>.*?<\/style>/is', '', $html);

        // Convert self-closing tags to proper closing tags
        $html = preg_replace('/<br\s*\/?>/i', '<br></br>', $html);
        $html = preg_replace('/<hr\s*\/?>/i', '<p>---</p>', $html);
        $html = preg_replace('/<img[^>]*>/i', '[Image]', $html);

        // Clean up empty paragraphs
        $html = preg_replace('/<p[^>]*>[\s&nbsp;]*<\/p>/i', '', $html);

        // Ensure proper paragraph structure
        $html = preg_replace('/^([^<])/m', '<p>$1</p>', $html);

        return $html;
    }

    private function processHtmlNodes($container, $nodes)
    {
        foreach ($nodes as $node) {
            if ($node->nodeType === XML_TEXT_NODE) {
                $text = trim($node->textContent);
                if (!empty($text)) {
                    $container->addText($text);
                }
            } elseif ($node->nodeType === XML_ELEMENT_NODE) {
                $this->processHtmlElement($container, $node);
            }
        }
    }

    private function processHtmlElement($container, $element)
    {
        $tagName = strtolower($element->tagName);

        switch ($tagName) {
            case 'p':
                if ($element->hasChildNodes()) {
                    $textRun = $container->addTextRun();
                    $this->processInlineElements($textRun, $element->childNodes);
                }
                $container->addTextBreak();
                break;

            case 'h1':
            case 'h2':
            case 'h3':
            case 'h4':
            case 'h5':
            case 'h6':
                $level = intval(substr($tagName, 1));
                $fontSize = max(18 - ($level * 2), 12);
                $container->addText($element->textContent, [
                    'bold' => true,
                    'size' => $fontSize
                ]);
                $container->addTextBreak();
                break;

            case 'strong':
            case 'b':
                $container->addText($element->textContent, ['bold' => true]);
                break;

            case 'em':
            case 'i':
                $container->addText($element->textContent, ['italic' => true]);
                break;

            case 'u':
                $container->addText($element->textContent, ['underline' => 'single']);
                break;

            case 'table':
                $this->processTable($container, $element);
                break;

            case 'ul':
            case 'ol':
                $this->processList($container, $element, $tagName === 'ol');
                break;

            case 'br':
                $container->addTextBreak();
                break;

            default:
                // For unknown elements, process their children
                if ($element->hasChildNodes()) {
                    $this->processHtmlNodes($container, $element->childNodes);
                }
                break;
        }
    }

    private function processInlineElements($textRun, $nodes)
    {
        foreach ($nodes as $node) {
            if ($node->nodeType === XML_TEXT_NODE) {
                $text = $node->textContent;
                if (!empty($text)) {
                    $textRun->addText($text);
                }
            } elseif ($node->nodeType === XML_ELEMENT_NODE) {
                $tagName = strtolower($node->tagName);
                $style = [];

                switch ($tagName) {
                    case 'strong':
                    case 'b':
                        $style['bold'] = true;
                        break;
                    case 'em':
                    case 'i':
                        $style['italic'] = true;
                        break;
                    case 'u':
                        $style['underline'] = 'single';
                        break;
                }

                $textRun->addText($node->textContent, $style);
            }
        }
    }

    private function processTable($container, $tableElement)
    {
        $table = $container->addTable([
            'borderSize' => 6,
            'borderColor' => '000000',
            'cellMargin' => 80
        ]);

        $rows = $tableElement->getElementsByTagName('tr');
        foreach ($rows as $rowElement) {
            $table->addRow();
            $cells = $rowElement->getElementsByTagName('td');
            if ($cells->length === 0) {
                $cells = $rowElement->getElementsByTagName('th');
            }

            foreach ($cells as $cellElement) {
                $cell = $table->addCell();
                $cell->addText($cellElement->textContent);
            }
        }
    }

    private function processList($container, $listElement, $isOrdered = false)
    {
        $items = $listElement->getElementsByTagName('li');
        foreach ($items as $index => $item) {
            $prefix = $isOrdered ? ($index + 1) . '. ' : '• ';
            $container->addText($prefix . trim($item->textContent));
        }
        $container->addTextBreak();
    }

    private function cleanupBackups($filePath)
    {
        $directory = dirname($filePath);
        $filename = basename($filePath);
        $backupPattern = $filename . '.backup.*';

        $backups = glob($directory . '/' . $backupPattern);

        if (count($backups) > 3) {
            // Sort by modification time (oldest first)
            usort($backups, function($a, $b) {
                return filemtime($a) - filemtime($b);
            });

            // Delete oldest backups, keep only 3 most recent
            $backupsToDelete = array_slice($backups, 0, -3);
            foreach ($backupsToDelete as $backup) {
                if (file_exists($backup)) {
                    unlink($backup);
                }
            }
        }
    }
}
