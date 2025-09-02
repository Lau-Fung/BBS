<?php

return [
    // Map your sheet headers (case-insensitive) → internal keys
    // Add synonyms you’ve seen in BBS.xlsx
    'columns' => [
        'carrier'            => ['carrier','شركة الاتصالات'],
        'msisdn'             => ['msisdn','phone','هاتف','رقم'],
        'sim_serial'         => ['sim serial','serial','iccid','سيريال'],
        'expiry'             => ['expiry','plan expiry','انتهاء'],
        'recharged'          => ['recharged','شحن'],
        'imei'               => ['imei','ايمي'],
        'device_model'       => ['device model','موديل الجهاز','fmc920'],
        'vehicle_plate'      => ['plate','vehicle','لوحة'],
        'sensor_id'          => ['sensor','bt id','bluetooth','حساس'],
        'sensor_model'       => ['sensor model','نوع حساس'],
        'tank_capacity'      => ['capacity','tank capacity','سعة الخزان'],
        'vehicle_status'     => ['status','الحالة'],
        'crm_no'             => ['crm','رقم crm'],
        'notes'              => ['notes','ملاحظات'],
        'supervisor'         => ['supervisor','مشرف'],
        'installed'          => ['installed','تركيب'],
        'installed_on'       => ['installed on','تاريخ التركيب'],
    ],

    // If a lookup (carrier/device model/sensor model) is missing:
    'create_missing_lookups' => true,

    // Date formats to try when parsing
    'date_formats' => ['d/m/Y','d-m-Y','Y-m-d','m/d/Y'],
];
