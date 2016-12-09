<?php
/**
 * Created by PhpStorm.
 * User: Nguyen Tuan Linh
 * Date: 2016-12-09
 * Time: 23:43
 */

namespace Katniss\Everdeen\Themes\Plugins\Polls\Repositories;


use Illuminate\Support\Facades\DB;
use Katniss\Everdeen\Exceptions\KatnissException;
use Katniss\Everdeen\Repositories\ModelRepository;
use Katniss\Everdeen\Themes\Plugins\Polls\Models\Poll;
use Katniss\Everdeen\Utils\AppConfig;

class PollRepository extends ModelRepository
{
    public function getById($id)
    {
        return Poll::findOrFail($id);
    }

    public function getPaged()
    {
        return Poll::orderBy('created_at', 'desc')->paginate(AppConfig::DEFAULT_ITEMS_PER_PAGE);
    }

    public function getAll()
    {
        return Poll::all();
    }

    public function create(array $localizedData = [], $multiChoice = false)
    {
        DB::beginTransaction();
        try {
            $poll = new Poll();
            $poll->multi_choice = $multiChoice;
            foreach ($localizedData as $locale => $transData) {
                $trans = $poll->translateOrNew($locale);
                $trans->name = $transData['name'];
                $trans->description = $transData['description'];
            }
            $poll->save();

            DB::commit();

            return $poll;
        } catch (\Exception $ex) {
            DB::rollBack();

            throw new KatnissException(trans('error.database_insert') . ' (' . $ex->getMessage() . ')');
        }
    }

    public function update(array $localizedData = [], $multiChoice = false)
    {
        $poll = $this->model();

        DB::beginTransaction();
        try {
            $poll->multi_choice = $multiChoice;

            $deletedLocales = [];
            foreach (supportedLocaleCodesOfInputTabs() as $locale) {
                if (isset($localizedData[$locale])) {
                    $transData = $localizedData[$locale];
                    $trans = $poll->translateOrNew($locale);
                    $trans->name = $transData['name'];
                    $trans->description = $transData['description'];
                } elseif ($poll->hasTranslation($locale)) {
                    $deletedLocales[] = $locale;
                }
            }
            $poll->save();

            if (!empty($deletedLocales)) {
                $poll->deleteTranslations($deletedLocales);
            }

            DB::commit();

            return $poll;
        } catch (\Exception $ex) {
            DB::rollBack();

            throw new KatnissException(trans('error.database_update') . ' (' . $ex->getMessage() . ')');
        }
    }

    public function delete()
    {
        $poll = $this->model();

        try {
            $poll->delete();
            return true;
        } catch (\Exception $ex) {
            throw new KatnissException(trans('error.database_delete') . ' (' . $ex->getMessage() . ')');
        }
    }
}