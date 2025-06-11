import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:lachaudiere_app/models/evenement.dart';

class ApiService {
  final String baseUrl;

  ApiService({required this.baseUrl});

  Future<List<Evenement>> fetchEvenements() async {
    final response = await http.get(Uri.parse('$baseUrl/evenements'));

    if (response.statusCode == 200) {
      final data = json.decode(utf8.decode(response.bodyBytes));
      List<dynamic> evenementsJsonList = data['evenements'];
      
      return evenementsJsonList.map((json) {
        final Map<String, dynamic> evenementData = json['evenement'];
        final Map<String, dynamic> linksData = json['links'];
        return Evenement.fromJson(evenementData, linksData);
      }).toList();
    } else {
      throw Exception('Impossible de charger les événements.');
    }
  }
}