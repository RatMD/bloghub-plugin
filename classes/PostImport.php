<?php declare(strict_types=1);

namespace RatMD\BlogHub\Classes;

use ApplicationException;
use Exception;
use RainLab\Blog\Models\Post;
use RainLab\Blog\Models\PostImport as RainLabPostImport;

class PostImport extends RainLabPostImport
{
    /**
     * Import data.
     * @param mixed $results
     * @param mixed $sessionKey
     * @return void
     * @throws ApplicationException
     * @throws mixed
     */
    public function importData($results, $sessionKey = null)
    {
        $firstRow = reset($results);

        //  Validation
        if ($this->auto_create_categories && !array_key_exists('categories', $firstRow)) {
            throw new ApplicationException('Please specify a match for the Categories column.');
        }

        // Import
        foreach ($results as $row => $data) {
            try {
                if (!$title = array_get($data, 'title')) {
                    $this->logSkipped($row, 'Missing post title');
                    continue;
                }

                // Find or create
                $post = Post::make();
                if ($this->update_existing) {
                    $post = $this->findDuplicatePost($data) ?: $post;
                }
                $postExists = $post->exists;

                // Set attributes
                $except = ['id', 'categories', 'author_email'];

                foreach (array_except($data, $except) as $attribute => $value) {
                    if (in_array($attribute, $post->getDates()) && empty($value)) {
                        continue;
                    }
                    if ($attribute == 'featured_image_urls') {
                        $value = is_array($value) ? $value : [$value];
                        $files = [];
                        foreach ($value AS $image) {
                            $filePath = storage_path('app/media/' . $image);
                            if (empty($image) || !file_exists($filePath) || !is_file($filePath)) {
                                continue;
                            }

                            $files[] = (new \System\Models\File)->fromFile(
                                $filePath,
                                basename($image)
                            );
                        }
                        if (!empty($files)) {
                            $post->featured_images = $files;
                        }
                    } else {
                        $post->{$attribute} = isset($value) ? $value : null;
                    }
                }

                if ($author = $this->findAuthorFromEmail($data)) {
                    $post->user_id = $author->id;
                }
                $post->forceSave();

                if ($categoryIds = $this->getCategoryIdsForPost($data)) {
                    $post->categories()->sync($categoryIds, false);
                }

                // Log results
                if ($postExists) {
                    $this->logUpdated();
                } else {
                    $this->logCreated();
                }
            }
            catch (Exception $ex) {
                throw $ex;
                $this->logError($row, $ex->getMessage());
            }
        }
    }
}
