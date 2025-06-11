import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:lachaudiere_app/models/categorie.dart';
import 'package:lachaudiere_app/models/evenement.dart';

class ApiService {
  final String baseUrl;
  ApiService({required this.baseUrl});

  Future<List<Evenement>> fetchEvenements() async {
    final response = await http.get(Uri.parse('$baseUrl/evenements'));
    if (response.statusCode == 200) {
      final data = json.decode(utf8.decode(response.bodyBytes));
      List<dynamic> evenementsJsonList = data['evenements'];
      return evenementsJsonList.map((json) => Evenement.fromJsonList(json)).toList();
    } else {
      throw Exception('Impossible de charger les événements.');
    }
  }

  Future<Map<String, dynamic>> fetchEvenementDetails(int eventId) async {
    final response = await http.get(Uri.parse('$baseUrl/evenements/$eventId'));
    if (response.statusCode == 200) {
      return json.decode(utf8.decode(response.bodyBytes));
    } else {
      throw Exception('Impossible de charger les détails de l\'événement.');
    }
  }

  Future<List<Categorie>> fetchCategories() async {
    final response = await http.get(Uri.parse('$baseUrl/categories'));
    if (response.statusCode == 200) {
      final data = json.decode(utf8.decode(response.bodyBytes));
      List<dynamic> categoriesJsonList = data['categories'];
      return categoriesJsonList.map((json) => Categorie.fromJson(json['categorie'])).toList();
    } else {
      throw Exception('Impossible de charger les catégories.');
    }
  }
}