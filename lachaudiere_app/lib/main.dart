import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:intl/date_symbol_data_local.dart';
import 'package:lachaudiere_app/providers/evenement_provider.dart';
import 'package:lachaudiere_app/screens/evenements_master.dart';
import 'package:lachaudiere_app/services/api_service.dart';


const String _baseUrl = "http://localhost:8000/api";

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  await initializeDateFormatting('fr_FR', null);
  
  runApp(const LaChaudiereApp());
}

class LaChaudiereApp extends StatelessWidget {
  const LaChaudiereApp({super.key});

  @override
  Widget build(BuildContext context) {
    final apiService = ApiService(baseUrl: _baseUrl);

    return ChangeNotifierProvider(
      create: (context) => EvenementProvider(apiService: apiService),
      child: MaterialApp(
        title: 'La Chaudière',
        theme: ThemeData(
          useMaterial3: true,
          colorScheme: ColorScheme.fromSeed(seedColor: const Color(0xFFE65100)),
          appBarTheme: const AppBarTheme(
            backgroundColor: Color(0xFFE65100),
            foregroundColor: Colors.white,
          ),
        ),
        home: const EvenementsMaster(),
        debugShowCheckedModeBanner: false,
      ),
    );
  }
}