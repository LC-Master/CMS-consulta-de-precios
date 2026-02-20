<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property string $id
 * @property string|null $subject_id
 * @property string|null $subject_type
 * @property string $causer_id
 * @property string $user_name
 * @property string $user_email
 * @property string $action
 * @property string $level
 * @property string|null $message
 * @property array<array-key, mixed>|null $properties
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property string|null $referer
 * @property string $created_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-write mixed $end_at
 * @property-write mixed $start_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent|null $subject
 * @property-read \App\Models\User|null $user
 * @method static \Database\Factories\ActivityLogFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereCauserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereProperties($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereReferer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereSubjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereSubjectType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereUserAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereUserEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereUserName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog withoutTrashed()
 */
	class ActivityLog extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $name
 * @property string|null $legal_name
 * @property string|null $tax_id
 * @property string|null $contact_person
 * @property string|null $contact_email
 * @property string|null $contact_phone
 * @property int $is_active
 * @property string|null $observations
 * @property int|null $supplier_id
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Campaign> $campaigns
 * @property-read int|null $campaigns_count
 * @property-write mixed $end_at
 * @property-write mixed $start_at
 * @property-read \App\Models\Supplier|null $supplier
 * @method static \Database\Factories\AgreementFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agreement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agreement newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agreement onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agreement query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agreement whereContactEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agreement whereContactPerson($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agreement whereContactPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agreement whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agreement whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agreement whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agreement whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agreement whereLegalName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agreement whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agreement whereObservations($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agreement whereSupplierId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agreement whereTaxId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agreement whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agreement withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agreement withoutTrashed()
 */
	class Agreement extends \Eloquent {}
}

namespace App\Models{
/**
 * @property-read \App\Models\User $user
 * @property string $id
 * @property string $title
 * @property string $start_at
 * @property string $end_at
 * @property string $status_id
 * @property string $department_id
 * @property int $user_id
 * @property int|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Agreement> $agreements
 * @property-read int|null $agreements_count
 * @property-read \App\Models\Department $department
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \App\Models\Status $status
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Store> $stores
 * @property-read int|null $stores_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TimeLineItem> $timeLineItems
 * @property-read int|null $time_line_items_count
 * @property-read \App\Models\User|null $updatedBy
 * @method static \Database\Factories\CampaignFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereDepartmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereEndAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereStartAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign withoutTrashed()
 */
	class Campaign extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $store_id
 * @property string|null $media_id
 * @property string $name
 * @property string|null $checksum
 * @property string|null $error_type
 * @property int $error_count
 * @property string $last_seen_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Media|null $media
 * @property-write mixed $end_at
 * @property-write mixed $start_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Store> $store
 * @property-read int|null $store_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CenterMediaError newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CenterMediaError newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CenterMediaError query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CenterMediaError whereChecksum($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CenterMediaError whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CenterMediaError whereErrorCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CenterMediaError whereErrorType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CenterMediaError whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CenterMediaError whereLastSeenAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CenterMediaError whereMediaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CenterMediaError whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CenterMediaError whereStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CenterMediaError whereUpdatedAt($value)
 */
	class CenterMediaError extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $store_id
 * @property string $snapshot_json
 * @property string $version_hash
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-write mixed $end_at
 * @property-write mixed $start_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CenterSnapshot newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CenterSnapshot newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CenterSnapshot query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CenterSnapshot whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CenterSnapshot whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CenterSnapshot whereSnapshotJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CenterSnapshot whereStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CenterSnapshot whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CenterSnapshot whereVersionHash($value)
 */
	class CenterSnapshot extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-write mixed $end_at
 * @property-write mixed $start_at
 * @method static \Database\Factories\DepartmentFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereUpdatedAt($value)
 */
	class Department extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $disk
 * @property string $path
 * @property string $name
 * @property string $mime_type
 * @property int $size
 * @property int|null $duration_seconds
 * @property string|null $checksum
 * @property int $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Campaign> $campaigns
 * @property-read int|null $campaigns_count
 * @property-write mixed $end_at
 * @property-write mixed $start_at
 * @property-read \App\Models\Thumbnail|null $thumbnail
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TimeLineItem> $timeLineItems
 * @property-read int|null $time_line_items_count
 * @method static \Database\Factories\MediaFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereChecksum($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereDisk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereDurationSeconds($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereMimeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereUpdatedAt($value)
 */
	class Media extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\StatusFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Status newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Status newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Status query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Status whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Status whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Status whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Status whereUpdatedAt($value)
 */
	class Status extends \Eloquent {}
}

namespace App\Models{
/**
 * Summary of Store
 *
 * @property-read \App\Models\StoreSyncState|null $syncState
 * @property int|null $ID
 * @property string|null $Name
 * @property string|null $StoreCode
 * @property string|null $Region
 * @property string|null $Address1
 * @property string|null $Address2
 * @property string|null $City
 * @property string|null $Country
 * @property string|null $FaxNumber
 * @property string|null $PhoneNumber
 * @property string|null $State
 * @property string|null $Zip
 * @property int|null $ParentStoreID
 * @property int|null $ScheduleHourMask1
 * @property int|null $ScheduleHourMask2
 * @property int|null $ScheduleHourMask3
 * @property int|null $ScheduleHourMask4
 * @property int|null $ScheduleHourMask5
 * @property int|null $ScheduleHourMask6
 * @property int|null $ScheduleHourMask7
 * @property int|null $ScheduleMinute
 * @property int|null $RetryCount
 * @property int|null $RetryDelay
 * @property string|null $LastUpdated
 * @property string|null $DBTimeStamp
 * @property string|null $AccountName
 * @property string|null $Password
 * @property int $AutoID
 * @property bool|null $Inactive
 * @property int|null $SyncedStoreStatus
 * @property string|null $SyncGuid
 * @property string|null $StoreKey
 * @property-read mixed $address
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CenterMediaError> $centerMediaErrors
 * @property-read int|null $center_media_errors_count
 * @property-read mixed $city
 * @property-read mixed $country
 * @property-read mixed $fax_number
 * @property-read mixed $id
 * @property-read mixed $name
 * @property-read mixed $phone_number
 * @property-read mixed $region
 * @property-write mixed $end_at
 * @property-write mixed $start_at
 * @property-read mixed $store_code
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store whereAccountName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store whereAddress1($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store whereAddress2($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store whereAutoID($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store whereDBTimeStamp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store whereFaxNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store whereID($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store whereInactive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store whereLastUpdated($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store whereParentStoreID($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store wherePhoneNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store whereRegion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store whereRetryCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store whereRetryDelay($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store whereScheduleHourMask1($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store whereScheduleHourMask2($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store whereScheduleHourMask3($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store whereScheduleHourMask4($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store whereScheduleHourMask5($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store whereScheduleHourMask6($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store whereScheduleHourMask7($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store whereScheduleMinute($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store whereStoreCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store whereStoreKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store whereSyncGuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store whereSyncedStoreStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store whereZip($value)
 */
	class Store extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $store_id
 * @property string|null $url
 * @property string|null $communication_key
 * @property string|null $placeholder_id
 * @property string|null $sync_started_at
 * @property string|null $sync_ended_at
 * @property array<array-key, mixed>|null $disk
 * @property string|null $uptimed_at
 * @property string|null $sync_status
 * @property string|null $last_synced_at
 * @property string|null $last_reported_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $sync_retries
 * @property string|null $last_sync_error
 * @property string|null $last_error_at
 * @property-read \App\Models\Media|null $placeholder
 * @property-write mixed $end_at
 * @property-write mixed $start_at
 * @property-read \App\Models\Store|null $store
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoreSyncState newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoreSyncState newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoreSyncState query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoreSyncState whereCommunicationKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoreSyncState whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoreSyncState whereDisk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoreSyncState whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoreSyncState whereLastErrorAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoreSyncState whereLastReportedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoreSyncState whereLastSyncError($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoreSyncState whereLastSyncedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoreSyncState wherePlaceholderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoreSyncState whereStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoreSyncState whereSyncEndedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoreSyncState whereSyncRetries($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoreSyncState whereSyncStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoreSyncState whereSyncStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoreSyncState whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoreSyncState whereUptimedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoreSyncState whereUrl($value)
 */
	class StoreSyncState extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $Country
 * @property int $HQID
 * @property \Illuminate\Support\Carbon $LastUpdated
 * @property string $State
 * @property int $ID
 * @property string $SupplierName
 * @property string $ContactName
 * @property string $Address1
 * @property string $Address2
 * @property string $City
 * @property string $Zip
 * @property string $EmailAddress
 * @property string $WebPageAddress
 * @property string $Code
 * @property string|null $DBTimeStamp
 * @property string $AccountNumber
 * @property string $TaxNumber
 * @property int $CurrencyID
 * @property string $PhoneNumber
 * @property string $FaxNumber
 * @property string $CustomText1
 * @property string $CustomText2
 * @property string $CustomText3
 * @property string $CustomText4
 * @property string $CustomText5
 * @property float $CustomNumber1
 * @property float $CustomNumber2
 * @property float $CustomNumber3
 * @property float $CustomNumber4
 * @property float $CustomNumber5
 * @property string|null $CustomDate1
 * @property string|null $CustomDate2
 * @property string|null $CustomDate3
 * @property string|null $CustomDate4
 * @property string|null $CustomDate5
 * @property string $Notes
 * @property string $Terms
 * @property string|null $SyncGuid
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Agreement> $agreements
 * @property-read int|null $agreements_count
 * @property-write mixed $end_at
 * @property-write mixed $start_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereAccountNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereAddress1($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereAddress2($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereContactName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereCurrencyID($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereCustomDate1($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereCustomDate2($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereCustomDate3($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereCustomDate4($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereCustomDate5($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereCustomNumber1($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereCustomNumber2($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereCustomNumber3($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereCustomNumber4($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereCustomNumber5($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereCustomText1($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereCustomText2($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereCustomText3($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereCustomText4($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereCustomText5($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereDBTimeStamp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereEmailAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereFaxNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereHQID($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereID($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereLastUpdated($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier wherePhoneNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereSupplierName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereSyncGuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereTaxNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereTerms($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereWebPageAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereZip($value)
 */
	class Supplier extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $path
 * @property string $name
 * @property string $mime_type
 * @property int $size
 * @property string $media_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Media $media
 * @property-write mixed $end_at
 * @property-write mixed $start_at
 * @method static \Database\Factories\ThumbnailFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Thumbnail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Thumbnail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Thumbnail query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Thumbnail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Thumbnail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Thumbnail whereMediaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Thumbnail whereMimeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Thumbnail whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Thumbnail wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Thumbnail whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Thumbnail whereUpdatedAt($value)
 */
	class Thumbnail extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $campaign_id
 * @property string $media_id
 * @property string $slot
 * @property int $position
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Campaign $campaign
 * @property-read \App\Models\Media $media
 * @property-write mixed $end_at
 * @property-write mixed $start_at
 * @method static \Database\Factories\TimeLineItemFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimeLineItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimeLineItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimeLineItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimeLineItem whereCampaignId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimeLineItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimeLineItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimeLineItem whereMediaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimeLineItem wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimeLineItem whereSlot($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimeLineItem whereUpdatedAt($value)
 */
	class TimeLineItem extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property \Illuminate\Support\Carbon|null $two_factor_confirmed_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-write mixed $end_at
 * @property-write mixed $start_at
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorConfirmedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorRecoveryCodes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutRole($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutTrashed()
 */
	class User extends \Eloquent {}
}

