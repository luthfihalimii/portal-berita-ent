<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <title>HalimiNews</title>
        <link>{{ route('home') }}</link>
        <atom:link href="{{ route('feed') }}" rel="self" type="application/rss+xml" />
        <description>Portal berita terkini, terpercaya, dan aktual dari HalimiNews.</description>
        <language>id</language>
@foreach ($articles as $article)
        <item>
            <title>{{ $article->title }}</title>
            <link>{{ route('news.show', $article->slug) }}</link>
            <guid isPermaLink="true">{{ route('news.show', $article->slug) }}</guid>
            <description><![CDATA[{{ $article->excerpt }}]]></description>
            <category>{{ $article->category->name }}</category>
            <pubDate>{{ $article->published_at->toRssString() }}</pubDate>
@if ($article->thumbnail)
            <enclosure url="{{ asset('storage/' . $article->thumbnail) }}" type="{{ \Illuminate\Support\Facades\Storage::disk('public')->mimeType($article->thumbnail) }}" />
@endif
        </item>
@endforeach
    </channel>
</rss>
