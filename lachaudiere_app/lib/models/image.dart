class ImageEvenement {
  final String url;
  final String legende;

  ImageEvenement({required this.url, required this.legende});

  factory ImageEvenement.fromJson(Map<String, dynamic> json, String baseUrl) {
    String relativeUrl = json['url_image'] as String? ?? json['url'] as String? ?? '';
    
    String finalUrl = relativeUrl.startsWith('/') 
        ? baseUrl + relativeUrl 
        : relativeUrl;

    return ImageEvenement(
      url: finalUrl,
      legende: json['legende'] as String? ?? '',
    );
  }
}