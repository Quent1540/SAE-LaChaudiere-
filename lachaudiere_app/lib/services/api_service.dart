import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:lachaudiere_app/models/categorie.dart';
import 'package:lachaudiere_app/models/evenement.dart';

class ApiService {
  final String _apiUrl;
  final String _domainUrl;

  ApiService({required String baseUrl}) 
      : _apiUrl = baseUrl,
        _domainUrl = "${Uri.parse(baseUrl).scheme}://${Uri.parse(baseUrl).host}:${Uri.parse(baseUrl).port}";

  Future<List<Evenement>> fetchEvenements() async {
    final response = await http.get(Uri.parse('$_apiUrl/evenements'));
    if (response.statusCode == 200) {
      final data = json.decode(utf8.decode(response.bodyBytes));
      List<dynamic> evenementsJsonList = data['evenements'];
      return evenementsJsonList.map((json) => Evenement.fromJsonList(json)).toList();
    } else {
      throw Exception('Impossible de charger les événements.');
    }
  }

  Future<Map<String, dynamic>> fetchEvenementDetails(int eventId) async {
    final response = await http.get(Uri.parse('$_apiUrl/evenements/$eventId'));
    if (response.statusCode == 200) {
      return json.decode(utf8.decode(response.bodyBytes));
    } else {
      throw Exception('Impossible de charger les détails de l\'événement.');
    }
  }

  String get domainUrl => _domainUrl;

  Future<List<Categorie>> fetchCategories() async {
    final response = await http.get(Uri.parse('$_apiUrl/categories'));
    if (response.statusCode == 200) {
      final data = json.decode(utf8.decode(response.bodyBytes));
      List<dynamic> categoriesJsonList = data['categories'];
      return categoriesJsonList.map((json) => Categorie.fromJson(json['categorie'])).toList();
    } else {
      throw Exception('Impossible de charger les catégories.');
    }
  }
}