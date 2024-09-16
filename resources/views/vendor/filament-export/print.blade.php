<!DOCTYPE html>
<html lang="{{ App::getLocale() }}" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $fileName ?? date()->format('d') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Kufi+Arabic:wght@100..900&display=swap" rel="stylesheet">
    <title>{{ $fileName }}</title>
    <style type="text/css" media="all">
        * {
            font-family: "Noto Kufi Arabic", system-ui;
            font-optical-sizing: auto;
            font-style: normal;

        }

        table {
            background: white;
            color: black;
            width: 100%;
            border-collapse: collapse;
            border-spacing: 0;
            font-family: sans-serif;
        }

        td,
        th {
            border-color: #ededed;
            border-style: solid;
            border-width: 1px;
            font-size: 13px;
            line-height: 2;
            overflow: hidden;
            padding-left: 6px;
            word-break: normal;
        }

        th {
            font-weight: normal;
        }

        table {
            page-break-after: auto
        }

        tr {
            page-break-inside: avoid;
            page-break-after: auto
        }

        td {
            page-break-inside: avoid;
            page-break-after: auto
        }
    </style>
</head>
<body>
    <h4 style="margin-bottom: 6px">
        {{$table_header}}
    </h4>
    <table>
        <tr>
            @foreach ($columns as $column)
                <th>
                    {{ $column->getLabel() }}
                </th>
            @endforeach
        </tr>
        @foreach ($rows as $row)
            <tr>
                @foreach ($columns as $column)
                    <td>
                        {{ $row[$column->getName()] }}
                    </td>
                @endforeach
            </tr>
        @endforeach
    </table>
</body>
</html>
