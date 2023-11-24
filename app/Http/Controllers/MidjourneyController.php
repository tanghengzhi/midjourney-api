<?php

namespace App\Http\Controllers;

use App\Models\MidjourneyTask;
use Ferranfg\MidjourneyPhp\Midjourney;
use Ferranfg\MidjourneyPhp\Prompts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

/**
 * @group Ai Creator
 */
class MidjourneyController extends Controller
{
    /**
     * Generate Images (Midjourney)
     *
     * @response 200
     */
    public function generate(Request $request)
    {
        $lock = Cache::lock('midjourney:generate', 60);

        if (!$lock->get()) {
            throw new TooManyRequestsHttpException(60);
        }

        $midjourneyTask = new MidjourneyTask();
        $midjourneyTask->save();

        $request->validate([
            'imagePrompts' => 'nullable|string',
            'textPrompt' => 'required|string',
            'parameters' => 'nullable|string',
            'discordChannelId' => 'required|string',
            'discordUserToken' => 'required|string',
        ]);

        $prompts = new Prompts(
            $request->imagePrompts,
            $request->textPrompt,
            $request->paramters,
        );
        $midjourney = new Midjourney($request->discordChannelId, $request->discordUserToken);

        // It takes about 1 minute to generate and upscale an image
        return $midjourney->generate($prompts);
    }
}
