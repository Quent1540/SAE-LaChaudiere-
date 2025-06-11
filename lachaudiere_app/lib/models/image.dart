class ImageEvenement {
  final String url;
  final String legende;

  ImageEvenement({required this.url, required this.legende});

  factory ImageEvenement.fromJson(Map<String, dynamic> json) {
    return ImageEvenement(
      url: json['url_image'] as String? ?? json['url'] as String? ?? '',
      legende: json['legende'] as String? ?? '',
    );
  }
}